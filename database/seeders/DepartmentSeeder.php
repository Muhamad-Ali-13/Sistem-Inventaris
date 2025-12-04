<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            [
                'nama' => 'IT & Teknologi',
                'deskripsi' => 'Departemen Teknologi Informasi dan Sistem'
            ],
            [
                'nama' => 'Sumber Daya Manusia',
                'deskripsi' => 'Departemen HRD dan Pengembangan SDM'
            ],
            [
                'nama' => 'Keuangan & Akuntansi',
                'deskripsi' => 'Departemen Keuangan dan Akuntansi'
            ],
            [
                'nama' => 'Pemasaran & Penjualan',
                'deskripsi' => 'Departemen Pemasaran dan Penjualan'
            ],
            [
                'nama' => 'Operasional',
                'deskripsi' => 'Departemen Operasional dan Logistik'
            ],
            [
                'nama' => 'Produksi',
                'deskripsi' => 'Departemen Produksi dan Manufaktur'
            ]
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}