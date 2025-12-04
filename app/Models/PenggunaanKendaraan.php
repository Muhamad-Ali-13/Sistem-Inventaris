<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenggunaanKendaraan extends Model
{
    use HasFactory;

    protected $table = 'penggunaan_kendaraan';
    protected $fillable = ['kendaraan_id', 'user_id', 'tanggal_mulai', 'tanggal_selesai', 'tujuan', 'status', 'disetujui_oleh', 'alasan_penolakan'];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}