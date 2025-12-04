<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class UserKaryawanSeeder extends Seeder
{
    public function run()
    {
        // // Ambil department
        // $deptIT = Department::where('nama', 'IT & Teknologi')->first();
        // $deptHRD = Department::where('nama', 'Sumber Daya Manusia')->first();
        // $deptKeuangan = Department::where('nama', 'Keuangan & Akuntansi')->first();
        // $deptPemasaran = Department::where('nama', 'Pemasaran & Penjualan')->first();
        // $deptOperasional = Department::where('nama', 'Operasional')->first();

        // Data untuk super admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@inventaris.com',
            'password' => Hash::make('password123'),
        ]);
        $superAdmin->assignRole('super admin');

        // Data untuk admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@inventaris.com',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        // // Data untuk karyawan
        // $karyawanUsers = [
        //     [
        //         'name' => 'Ahmad Wijaya',
        //         'email' => 'ahmad.wijaya@company.com',
        //         'password' => Hash::make('password123'),
        //         'karyawan_data' => [
        //             'nama' => 'Ahmad Wijaya',
        //             'telepon' => '081234567890',
        //             'department_id' => $deptIT->id,
        //             'jabatan' => 'Staff IT',
        //         ]
        //     ],
        //     [
        //         'name' => 'Siti Rahayu',
        //         'email' => 'siti.rahayu@company.com',
        //         'password' => Hash::make('password123'),
        //         'karyawan_data' => [
        //             'nama' => 'Siti Rahayu',
        //             'telepon' => '081234567891',
        //             'department_id' => $deptHRD->id,
        //             'jabatan' => 'Staff HRD',
        //         ]
        //     ],
        //     [
        //         'name' => 'Budi Santoso',
        //         'email' => 'budi.santoso@company.com',
        //         'password' => Hash::make('password123'),
        //         'karyawan_data' => [
        //             'nama' => 'Budi Santoso',
        //             'telepon' => '081234567892',
        //             'department_id' => $deptKeuangan->id,
        //             'jabatan' => 'Staff Keuangan',
        //         ]
        //     ],
        //     [
        //         'name' => 'Dewi Lestari',
        //         'email' => 'dewi.lestari@company.com',
        //         'password' => Hash::make('password123'),
        //         'karyawan_data' => [
        //             'nama' => 'Dewi Lestari',
        //             'telepon' => '081234567893',
        //             'department_id' => $deptPemasaran->id,
        //             'jabatan' => 'Staff Pemasaran',
        //         ]
        //     ],
        //     [
        //         'name' => 'Rudi Hermawan',
        //         'email' => 'rudi.hermawan@company.com',
        //         'password' => Hash::make('password123'),
        //         'karyawan_data' => [
        //             'nama' => 'Rudi Hermawan',
        //             'telepon' => '081234567894',
        //             'department_id' => $deptOperasional->id,
        //             'jabatan' => 'Staff Operasional',
        //         ]
        //     ]
        // ];

        // foreach ($karyawanUsers as $userData) {
        //     $user = User::create([
        //         'name' => $userData['name'],
        //         'email' => $userData['email'],
        //         'password' => $userData['password'],
        //     ]);
        //     $user->assignRole('karyawan');

        //     // Buat data karyawan
        //     Karyawan::create(array_merge(
        //         $userData['karyawan_data'],
        //         ['user_id' => $user->id, 'email' => $userData['email']]
        //     ));
        // }
    }
}