<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\ItemRequest;
use App\Models\VehicleUsage;
use App\Models\Item;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Category;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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

        $itemRequests = Request::with(['item', 'user', 'approver'])
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

        $popularItems = Item::withCount(['itemRequests as request_count' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->orderBy('request_count', 'desc')
            ->take(10)
            ->get();

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

        return redirect()->route('reports.index')
            ->with('success', 'Export feature will be implemented soon.')
            ->withInput();
    }
}
