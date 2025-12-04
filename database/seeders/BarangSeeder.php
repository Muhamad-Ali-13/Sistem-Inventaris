<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\Kategori;

class BarangSeeder extends Seeder
{
    public function run()
    {
        $kategoriElektronik = Kategori::where('nama', 'Elektronik')->first();
        $kategoriATK = Kategori::where('nama', 'Alat Tulis Kantor')->first();
        $kategoriIT = Kategori::where('nama', 'IT Equipment')->first();

        $barangs = [
            // Elektronik
            [
                'nama' => 'Laptop Dell Latitude',
                'kode' => 'LP-DLL-001',
                'deskripsi' => 'Laptop untuk kerja sehari-hari',
                'kategori_id' => $kategoriElektronik->id,
                'stok' => 15,
                'stok_minimal' => 5,
                'harga' => 12500000,
                'lokasi' => 'Gudang IT'
            ],
            [
                'nama' => 'Monitor 24 inch',
                'kode' => 'MON-24-001',
                'deskripsi' => 'Monitor LCD 24 inch full HD',
                'kategori_id' => $kategoriElektronik->id,
                'stok' => 20,
                'stok_minimal' => 8,
                'harga' => 2500000,
                'lokasi' => 'Gudang IT'
            ],
            [
                'nama' => 'Printer LaserJet',
                'kode' => 'PRN-LJ-001',
                'deskripsi' => 'Printer laser warna',
                'kategori_id' => $kategoriElektronik->id,
                'stok' => 8,
                'stok_minimal' => 3,
                'harga' => 3500000,
                'lokasi' => 'Gudang Umum'
            ],

            // Alat Tulis Kantor
            [
                'nama' => 'Bolpoin Standard',
                'kode' => 'ATK-BP-001',
                'deskripsi' => 'Bolpoin warna hitam',
                'kategori_id' => $kategoriATK->id,
                'stok' => 200,
                'stok_minimal' => 50,
                'harga' => 2500,
                'lokasi' => 'Ruang ATK'
            ],
            [
                'nama' => 'Buku Catatan A4',
                'kode' => 'ATK-BK-001',
                'deskripsi' => 'Buku catatan ukuran A4 100 halaman',
                'kategori_id' => $kategoriATK->id,
                'stok' => 100,
                'stok_minimal' => 30,
                'harga' => 15000,
                'lokasi' => 'Ruang ATK'
            ],
            [
                'nama' => 'Stapler Max',
                'kode' => 'ATK-ST-001',
                'deskripsi' => 'Stapler besar dengan isi staples',
                'kategori_id' => $kategoriATK->id,
                'stok' => 25,
                'stok_minimal' => 10,
                'harga' => 45000,
                'lokasi' => 'Ruang ATK'
            ],

            // IT Equipment
            [
                'nama' => 'Mouse Wireless',
                'kode' => 'IT-MS-001',
                'deskripsi' => 'Mouse wireless ergonomis',
                'kategori_id' => $kategoriIT->id,
                'stok' => 35,
                'stok_minimal' => 15,
                'harga' => 150000,
                'lokasi' => 'Gudang IT'
            ],
            [
                'nama' => 'Keyboard Mechanical',
                'kode' => 'IT-KB-001',
                'deskripsi' => 'Keyboard mechanical RGB',
                'kategori_id' => $kategoriIT->id,
                'stok' => 18,
                'stok_minimal' => 8,
                'harga' => 450000,
                'lokasi' => 'Gudang IT'
            ],
            [
                'nama' => 'Webcam HD',
                'kode' => 'IT-WC-001',
                'deskripsi' => 'Webcam 1080p untuk meeting online',
                'kategori_id' => $kategoriIT->id,
                'stok' => 12,
                'stok_minimal' => 5,
                'harga' => 300000,
                'lokasi' => 'Gudang IT'
            ]
        ];

        foreach ($barangs as $barang) {
            Barang::create($barang);
        }
    }
}