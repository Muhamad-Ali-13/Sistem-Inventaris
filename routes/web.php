<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenggunaanKendaraanController;
use App\Http\Controllers\PermintaanBarangController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::resource('roles', RoleController::class)
    ->middleware('auth');
Route::get('roles/create', [RoleController::class, 'create'])
    ->middleware('auth', 'role:superadmin');
Route::post('roles', [RoleController::class, 'store'])
    ->middleware('auth', 'role:superadmin');
Route::get('roles/{role}/edit', [RoleController::class, 'edit'])
    ->middleware('auth', 'role:superadmin');
Route::put('roles/{role}', [RoleController::class, 'update'])
    ->middleware('auth', 'role:superadmin');
Route::delete('roles/{role}', [RoleController::class, 'destroy'])
    ->middleware('auth', 'role:superadmin');


Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Management Routes - GUNAKAN AUTH DULU, PERMISSION NANTI
    Route::resource('departments', DepartmentController::class)->except(['show']);
    Route::resource('karyawan', KaryawanController::class);
    Route::resource('kategori', KategoriController::class)->except(['show']);
    Route::resource('barang', BarangController::class);
    Route::resource('users', UserController::class);
    Route::resource('transaksi', TransaksiController::class);
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::resource('kendaraan', KendaraanController::class);
    Route::resource('permintaan-barang', PermintaanBarangController::class);
    Route::resource('penggunaan-kendaraan', PenggunaanKendaraanController::class);
    
    // Approval routes

    Route::put('/permintaan-barang/{permintaanBarang}', [PermintaanBarangController::class, 'update'])->name('permintaan-barang.update');
    Route::put('permintaan-barang/{permintaan_barang}/approve', [PermintaanBarangController::class, 'approve'])->name('permintaan-barang.approve');
    Route::put('permintaan-barang/{permintaan_barang}/reject', [PermintaanBarangController::class, 'reject'])->name('permintaan-barang.reject');
    
    Route::put('penggunaan-kendaraan/{penggunaan_kendaraan}/approve', [PenggunaanKendaraanController::class, 'approve'])->name('penggunaan-kendaraan.approve');
    Route::put('penggunaan-kendaraan/{penggunaan_kendaraan}/reject', [PenggunaanKendaraanController::class, 'reject'])->name('penggunaan-kendaraan.reject');
    Route::put('penggunaan-kendaraan/{penggunaan_kendaraan}/return', [PenggunaanKendaraanController::class, 'return'])->name('penggunaan-kendaraan.return');
    
    // Reports
    Route::get('laporan', [LaporanController::class, 'index'])->name('reports.index');
    Route::get('laporan/export', [LaporanController::class, 'export'])->name('reports.export');
});