<?php

namespace App\Http\Controllers;

use App\Exports\ItemRequestsExport;
use App\Exports\TransactionsExport;
use App\Exports\VehicleUsageExport;
use App\Models\Transaction;
use App\Models\ItemRequest;
use App\Models\VehicleUsage;
use App\Models\Item;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Category;
use App\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Manual permission check - alternatif tanpa middleware
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $user = Auth::user();

            // Safe role check
            if ($this->userHasRole($user, 'super admin')) {
                return $next($request);
            }

            if ($this->userHasRole($user, 'admin')) {
                return $next($request);
            }

            // Karyawan tidak bisa akses
            abort(403, 'Unauthorized action.');
        });
    }

    /**
     * Safe method to check user role
     */
    private function userHasRole($user, $role)
    {
        // Method 1: Using hasRole if method exists
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }

        // Method 2: Manual check through roles
        foreach ($user->roles as $userRole) {
            if ($userRole->name === $role) {
                return true;
            }
        }

        return false;
    }

    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $transactions = Transaction::with(['item', 'user'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        $transactionSummary = [
            'total_in' => $transactions->where('type', 'in')->sum('quantity'),
            'total_out' => $transactions->where('type', 'out')->sum('quantity'),
            'total_transactions' => $transactions->count(),
        ];

        $itemRequests = ItemRequest::with(['item', 'user', 'approver'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $requestSummary = [
            'pending' => $itemRequests->where('status', 'pending')->count(),
            'approved' => $itemRequests->where('status', 'approved')->count(),
            'rejected' => $itemRequests->where('status', 'rejected')->count(),
            'total_requests' => $itemRequests->count(),
        ];

        $vehicleUsage = VehicleUsage::with(['vehicle', 'user', 'approver'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $vehicleSummary = [
            'pending' => $vehicleUsage->where('status', 'pending')->count(),
            'approved' => $vehicleUsage->where('status', 'approved')->count(),
            'rejected' => $vehicleUsage->where('status', 'rejected')->count(),
            'returned' => $vehicleUsage->where('status', 'returned')->count(),
            'total_usage' => $vehicleUsage->count(),
        ];

        // ALTERNATIF 1: Gunakan join manual untuk popular items
        $popularItems = DB::table('items')
            ->select('items.id', 'items.name', DB::raw('COUNT(requests.id) as request_count'))
            ->leftJoin('requests', function ($join) use ($startDate, $endDate) {
                $join->on('items.id', '=', 'requests.item_id')
                    ->whereBetween('requests.created_at', [$startDate, $endDate]);
            })
            ->groupBy('items.id', 'items.name')
            ->orderBy('request_count', 'desc')
            ->take(10)
            ->get();

        // ALTERNATIF 2: Atau gunakan collection method
        $popularItemsCollection = [];
        foreach ($itemRequests as $request) {
            $itemId = $request->item_id;
            $itemName = $request->item->name;

            if (!isset($popularItemsCollection[$itemId])) {
                $popularItemsCollection[$itemId] = [
                    'name' => $itemName,
                    'request_count' => 0
                ];
            }
            $popularItemsCollection[$itemId]['request_count']++;
        }

        // Urutkan dan ambil 10 teratas
        usort($popularItemsCollection, function ($a, $b) {
            return $b['request_count'] - $a['request_count'];
        });
        $popularItems = array_slice($popularItemsCollection, 0, 10);

        return view('reports.index', compact(
            'transactions',
            'itemRequests',
            'vehicleUsage',
            'transactionSummary',
            'requestSummary',
            'vehicleSummary',
            'popularItems',
            'startDate',
            'endDate'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $type = $request->get('type', 'transactions');
        $format = $request->get('format', 'excel');

        // Validasi format
        if (!in_array($format, ['excel', 'pdf'])) {
            return redirect()->route('reports.index')
                ->with('error', 'Invalid export format.')
                ->withInput();
        }

        try {
            $filename = $this->generateFilename($type, $format, $startDate, $endDate);

            if ($format === 'excel') {
                return $this->exportExcel($type, $startDate, $endDate, $filename);
            } else {
                return $this->exportPdf($type, $startDate, $endDate, $filename);
            }
        } catch (\Exception $e) {
            return redirect()->route('reports.index')
                ->with('error', 'Export failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function exportExcel($type, $startDate, $endDate, $filename)
    {
        switch ($type) {
            case 'transactions':
                return Excel::download(new TransactionsExport($startDate, $endDate), $filename);
            case 'item-requests':
                return Excel::download(new ItemRequestsExport($startDate, $endDate), $filename);
            case 'vehicle-usage':
                return Excel::download(new VehicleUsageExport($startDate, $endDate), $filename);
            default:
                throw new \Exception('Invalid export type');
        }
    }

    private function exportPdf($type, $startDate, $endDate, $filename)
    {
        // Get data for PDF
        $data = $this->getExportData($type, $startDate, $endDate);

        $pdf = Pdf::loadView('exports.report-pdf', [
            'data' => $data,
            'type' => $type,
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ]);

        return $pdf->download($filename);
    }

    private function getExportData($type, $startDate, $endDate)
    {
        $data = [];

        switch ($type) {
            case 'transactions':
                $data['transactions'] = Transaction::with(['item', 'user'])
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->get();
                $data['transactionSummary'] = [
                    'total_in' => $data['transactions']->where('type', 'in')->sum('quantity'),
                    'total_out' => $data['transactions']->where('type', 'out')->sum('quantity'),
                    'total_transactions' => $data['transactions']->count(),
                ];
                break;

            case 'item-requests':
                $data['itemRequests'] = ItemRequest::with(['item', 'user', 'approver'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();
                $data['requestSummary'] = [
                    'pending' => $data['itemRequests']->where('status', 'pending')->count(),
                    'approved' => $data['itemRequests']->where('status', 'approved')->count(),
                    'rejected' => $data['itemRequests']->where('status', 'rejected')->count(),
                    'total_requests' => $data['itemRequests']->count(),
                ];
                break;

            case 'vehicle-usage':
                $data['vehicleUsage'] = VehicleUsage::with(['vehicle', 'user', 'approver'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();
                $data['vehicleSummary'] = [
                    'pending' => $data['vehicleUsage']->where('status', 'pending')->count(),
                    'approved' => $data['vehicleUsage']->where('status', 'approved')->count(),
                    'rejected' => $data['vehicleUsage']->where('status', 'rejected')->count(),
                    'returned' => $data['vehicleUsage']->where('status', 'returned')->count(),
                    'total_usage' => $data['vehicleUsage']->count(),
                ];
                break;
        }

        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;

        return $data;
    }

    private function generateFilename($type, $format, $startDate, $endDate)
    {
        $extension = $format === 'excel' ? 'xlsx' : 'pdf';
        $dateRange = Carbon::parse($startDate)->format('Ymd') . '_' . Carbon::parse($endDate)->format('Ymd');

        return "{$type}_report_{$dateRange}.{$extension}";
    }
}
