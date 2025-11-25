<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ItemRequestController;
use App\Http\Controllers\VehicleUsageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Management Routes - GUNAKAN AUTH DULU, PERMISSION NANTI
    Route::resource('departments', DepartmentController::class)->except(['show']);
    Route::resource('employees', EmployeeController::class);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('items', ItemController::class);
    Route::resource('users', UserController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('item-requests', RequestController::class);
    Route::resource('vehicle-usage', VehicleUsageController::class);
    
    // Approval routes
    Route::put('item-requests/{item_request}/approve', [RequestController::class, 'approve'])->name('item-requests.approve');
    Route::put('item-requests/{item_request}/reject', [RequestController::class, 'reject'])->name('item-requests.reject');
    
    Route::put('vehicle-usage/{vehicle_usage}/approve', [VehicleUsageController::class, 'approve'])->name('vehicle-usage.approve');
    Route::put('vehicle-usage/{vehicle_usage}/reject', [VehicleUsageController::class, 'reject'])->name('vehicle-usage.reject');
    Route::put('vehicle-usage/{vehicle_usage}/return', [VehicleUsageController::class, 'return'])->name('vehicle-usage.return');
    
    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
});