<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'tanggal',
        'jenis',
        'barang_id',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'keterangan',
        'user_id',
        'permintaan_barang_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permintaanBarang()
    {
        return $this->belongsTo(PermintaanBarang::class);
    }
}
