<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        $kategories = [
            [
                'nama' => 'Elektronik',
                'deskripsi' => 'Barang-barang elektronik dan peralatan digital'
            ],
            [
                'nama' => 'Alat Tulis Kantor',
                'deskripsi' => 'Peralatan tulis menulis dan kebutuhan kantor'
            ],
            [
                'nama' => 'Furniture',
                'deskripsi' => 'Perabotan dan furniture kantor'
            ],
            [
                'nama' => 'Kendaraan',
                'deskripsi' => 'Kendaraan operasional perusahaan'
            ],
            [
                'nama' => 'Perlengkapan Kebersihan',
                'deskripsi' => 'Alat dan bahan kebersihan'
            ],
            [
                'nama' => 'IT Equipment',
                'deskripsi' => 'Peralatan dan aksesori komputer'
            ]
        ];

        foreach ($kategories as $kategori) {
            Kategori::create($kategori);
        }
    }
}