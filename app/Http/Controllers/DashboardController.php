<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Category;
use App\Models\Item;
use App\Models\Vehicle;
use App\Models\Transaction;
use App\Models\ItemRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Safe role check dengan error handling
        $isAdmin = $this->checkUserRole($user, ['super admin', 'admin']);

        // Hanya super admin dan admin yang bisa akses dashboard lengkap
        if ($isAdmin) {
            $data = [
                'departmentCount' => Schema::hasTable('departments') ? Department::count() : 0,
                'employeesCount' => Schema::hasTable('employees') ? Employee::count() : 0,
                'categoryCount' => Schema::hasTable('categories') ? Category::count() : 0,
                'itemCount' => Schema::hasTable('items') ? Item::count() : 0,
                'vehicleCount' => Schema::hasTable('vehicles') ? Vehicle::count() : 0,
                'requestCount' => Schema::hasTable('requests') ? ItemRequest::count() : 0,
                'transactionOutCount' => Schema::hasTable('transactions') ? Transaction::where('type', 'out')->count() : 0,
                'transactionOutThisMonth' => Schema::hasTable('transactions') ? Transaction::where('type', 'out')
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->count() : 0,
                'lowStockItems' => Schema::hasTable('items') ? Item::where('stock', '<', 10)->get() : collect(),
                'pendingRequests' => Schema::hasTable('requests') ? ItemRequest::where('status', 'pending')->get() : collect(),
                'popularItems' => Schema::hasTable('items') ? $this->getPopularItems() : collect(),
            ];

            return view('dashboard', $data);
        } else {

            // Gunakan query langsung tanpa relasi
            $userRequestsCount = Schema::hasTable('requests') ? \App\Models\ItemRequest::where('user_id', $user->id)->count() : 0;
            $userVehicleUsageCount = Schema::hasTable('vehicle_usage') ? \App\Models\VehicleUsage::where('user_id', $user->id)->count() : 0;
            $userPendingRequestsCount = Schema::hasTable('requests') ? \App\Models\ItemRequest::where('user_id', $user->id)->where('status', 'pending')->count() : 0;

            return view('dashboard-karyawan', compact(
                'user',
                'userRequestsCount',
                'userVehicleUsageCount',
                'userPendingRequestsCount'
            ));
        }
    }

    /**
     * Safe method untuk check user role
     */
    private function checkUserRole($user, $roles)
    {
        try {
            // Coba gunakan method hasRole dari Spatie
            if (method_exists($user, 'hasRole')) {
                return $user->hasRole($roles);
            }

            // Fallback: cek manual menggunakan relasi
            $userRoles = $user->roles()->pluck('name')->toArray();
            return !empty(array_intersect((array)$roles, $userRoles));
        } catch (\Exception $e) {
            // Jika ada error, return false
            return false;
        }
    }

    private function getPopularItems()
    {
        if (!Schema::hasTable('items') || !Schema::hasTable('transactions')) {
            return collect();
        }

        return Item::withCount(['transactions as transaction_count' => function ($query) {
            $query->where('type', 'out')
                ->where('created_at', '>=', Carbon::now()->subMonth());
        }])
            ->orderBy('transaction_count', 'desc')
            ->take(5)
            ->get();
    }
}
