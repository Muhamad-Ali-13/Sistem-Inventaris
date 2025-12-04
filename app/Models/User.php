<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'karyawan_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }


    public function penggunaanKendaraan()
    {
        return $this->hasMany(PenggunaanKendaraan::class);
    }

    public function permintaanBarang()
    {
        return $this->hasMany(PermintaanBarang::class, 'user_id');
    }

    // Relationship ke permintaan yang disetujui user (sebagai approver)
    public function approvedPermintaanBarang()
    {
        return $this->hasMany(PermintaanBarang::class, 'disetujui_oleh');
    }

    public function disetujuiPenggunaanKendaraan()
    {
        return $this->hasMany(PenggunaanKendaraan::class, 'disetujui_oleh');
    }

    // Relasi baru ke karyawan
    // Relasi ke karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    // Cek apakah user adalah superadmin
    public function isSuperAdmin()
    {
        return $this->hasRole('super admin');
    }

    // Cek apakah user adalah admin
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    // Cek apakah user adalah karyawan
    public function isKaryawan()
    {
        return $this->hasRole('karyawan');
    }

    // Method untuk mendapatkan data karyawan
    public function getDataKaryawan()
    {
        return $this->karyawan ?? null;
    }

    // Method untuk mendapatkan total pengeluaran
    // Method untuk mendapatkan total pengeluaran
    public function getTotalPengeluaranBulanIni()
    {
        $total = $this->permintaanBarang()
            ->where('status', 'disetujui')
            ->whereMonth('created_at', now()->month)
            ->sum('total_harga');

        return $total ?: 0;
    }

    // Method untuk mendapatkan riwayat pengeluaran
    public function getRiwayatPengeluaran($bulan = null, $tahun = null)
    {
        $query = $this->permintaanBarang()
            ->where('status', 'disetujui')
            ->with('barang');

        if ($bulan) {
            $query->whereMonth('created_at', $bulan);
        }

        if ($tahun) {
            $query->whereYear('created_at', $tahun);
        }

        return $query->get();
    }
}
