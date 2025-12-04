<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolePermissionSeeder::class,
            // DepartmentSeeder::class,      // Harus dijalankan sebelum KaryawanSeeder
            KategoriSeeder::class,        // Harus dijalankan sebelum BarangSeeder
            UserKaryawanSeeder::class,    // Menggantikan UserSeeder
            // BarangSeeder::class,          // Butuh Kategori
            // KendaraanSeeder::class,       // Tambahkan ini
        ]);
    }
}