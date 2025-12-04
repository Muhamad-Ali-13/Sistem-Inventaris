<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kendaraan;

class KendaraanSeeder extends Seeder
{
    public function run()
    {
        $kendaraans = [
            [
                'nama' => 'Toyota Avanza',
                'plat_nomor' => 'B 1234 ABC',
                'tipe' => 'MPV',
                'harga' => 185000000,
                'konsumsi_bahan_bakar' => 12,
                'perawatan_terakhir' => '2024-10-15',
                'tersedia' => true
            ],
            [
                'nama' => 'Honda Brio',
                'plat_nomor' => 'B 5678 DEF',
                'tipe' => 'City Car',
                'harga' => 165000000,
                'konsumsi_bahan_bakar' => 15,
                'perawatan_terakhir' => '2024-11-01',
                'tersedia' => true
            ],
            [
                'nama' => 'Mitsubishi Pajero',
                'plat_nomor' => 'B 9012 GHI',
                'tipe' => 'SUV',
                'harga' => 550000000,
                'konsumsi_bahan_bakar' => 8,
                'perawatan_terakhir' => '2024-09-20',
                'tersedia' => false
            ],
            [
                'nama' => 'Toyota Hiace',
                'plat_nomor' => 'B 3456 JKL',
                'tipe' => 'Minibus',
                'harga' => 420000000,
                'konsumsi_bahan_bakar' => 10,
                'perawatan_terakhir' => '2024-10-30',
                'tersedia' => true
            ],
            [
                'nama' => 'Daihatsu Xenia',
                'plat_nomor' => 'B 7890 MNO',
                'tipe' => 'MPV',
                'harga' => 175000000,
                'konsumsi_bahan_bakar' => 13,
                'perawatan_terakhir' => '2024-11-10',
                'tersedia' => true
            ]
        ];

        foreach ($kendaraans as $kendaraan) {
            Kendaraan::create($kendaraan);
        }
    }
}