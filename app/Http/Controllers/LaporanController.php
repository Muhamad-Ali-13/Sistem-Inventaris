<?php

namespace App\Http\Controllers;

use App\Exports\ItemRequestsExport;
use App\Exports\TransactionsExport;
use App\Exports\VehicleUsageExport;
use App\Models\PenggunaanKendaraan;
use App\Models\PermintaanBarang;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
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

        $transactions = Transaksi::with(['barang', 'user'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get();

        $transactionSummary = [
            'total_in' => $transactions->where('type', 'in')->sum('quantity'),
            'total_out' => $transactions->where('type', 'out')->sum('quantity'),
            'total_transactions' => $transactions->count(),
        ];

        $itemRequests = PermintaanBarang::with(['barang', 'user', 'approver'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $requestSummary = [
            'pending' => $itemRequests->where('status', 'pending')->count(),
            'approved' => $itemRequests->where('status', 'approved')->count(),
            'rejected' => $itemRequests->where('status', 'rejected')->count(),
            'total_requests' => $itemRequests->count(),
        ];

        $vehicleUsage = PenggunaanKendaraan::with(['kendaraan', 'user', 'approver'])
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
        $popularItems = DB::table('barang')
            ->select('barang.id', 'barang.nama', DB::raw('COUNT(permintaan_barang.id) as request_count'))
            ->leftJoin('permintaan_barang', function ($join) use ($startDate, $endDate) {
                $join->on('barang.id', '=', 'permintaan_barang.barang_id')
                    ->whereBetween('permintaan_barang.created_at', [$startDate, $endDate]);
            })
            ->groupBy('barang.id', 'barang.nama')
            ->orderBy('request_count', 'desc')
            ->take(10)
            ->get();

        // ALTERNATIF 2: Atau gunakan collection method
        $popularItemsCollection = [];
        foreach ($itemRequests as $request) {
            $itemId = $request->barang_id;
            $itemName = $request->barang->nama;

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

        return view('laporans.index', compact(
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
                $data['transactions'] = Transaksi::with(['barang', 'user'])
                    ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                    ->get();
                $data['transactionSummary'] = [
                    'total_in' => $data['transactions']->where('type', 'in')->sum('quantity'),
                    'total_out' => $data['transactions']->where('type', 'out')->sum('quantity'),
                    'total_transactions' => $data['transactions']->count(),
                ];
                break;

            case 'item-requests':
                $data['itemRequests'] = PermintaanBarang::with(['barang', 'user', 'approver'])
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
                $data['vehicleUsage'] = PenggunaanKendaraan::with(['kendaraan', 'user', 'approver'])
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
