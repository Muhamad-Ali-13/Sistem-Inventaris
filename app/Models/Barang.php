<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $fillable = ['nama', 'kode', 'deskripsi', 'kategori_id', 'stok', 'stok_minimal', 'harga', 'lokasi'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
    public function permintaanBarang()
    {
        return $this->hasMany(permintaanBarang::class);
    }

    // Scope untuk barang dengan stok rendah
    public function scopeStokRendah($query)
    {
        return $query->where('stok', '<', DB::raw('stok_minimal'));
    }
    // app/Models/Barang.php
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    // Method untuk update stok dan harga rata-rata
    public function updateStokDanHarga($jumlah, $hargaSatuan, $jenis)
    {
        if ($jenis == 'masuk') {
            // Hitung harga rata-rata baru
            $totalNilaiLama = $this->stok * $this->harga;
            $totalNilaiMasuk = $jumlah * $hargaSatuan;
            $stokBaru = $this->stok + $jumlah;
            $hargaBaru = $stokBaru > 0 ? ($totalNilaiLama + $totalNilaiMasuk) / $stokBaru : 0;

            $this->stok = $stokBaru;
            $this->harga = $hargaBaru;
        } elseif ($jenis == 'keluar') {
            if ($this->stok < $jumlah) {
                throw new \Exception('Stok tidak mencukupi');
            }
            $this->stok -= $jumlah;
            // Harga rata-rata tidak berubah saat keluar
        }

        $this->save();
    }
}
