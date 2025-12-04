<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $table = 'kendaraan'; // Pastikan nama tabel sesuai
    
    protected $fillable = [
        'nama',
        'plat_nomor', 
        'tipe',
        'harga',
        'konsumsi_bahan_bakar',
        'perawatan_terakhir',
        'tersedia'
    ];
    
    protected $casts = [
        'perawatan_terakhir' => 'date',
        'tersedia' => 'boolean',
        'harga' => 'decimal:2'
    ];
    
    // Relasi jika ada
    public function penggunaanKendaraan()
    {
        return $this->hasMany(PenggunaanKendaraan::class);
    }
}