<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage departments',
            'manage employees',
            'manage categories',
            'manage items',
            'manage users',
            'manage transactions',
            'manage roles',
            'manage vehicle',
            'manage requests',
            'manage vehicle usage',
            'view reports',
            'view dashboard',
            'request item',
            'request vehicle'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $superAdminRole = Role::create(['name' => 'super admin']);
        $adminRole = Role::create(['name' => 'admin']);
        $karyawanRole = Role::create(['name' => 'karyawan']);

        // Assign permissions
        $superAdminRole->givePermissionTo(Permission::all());
        
        $adminRole->givePermissionTo([
            'manage departments',
            'manage employees',
            'manage categories',
            'manage items',
            'manage users',
            'manage transactions',
            'manage vehicle',
            'manage requests',
            'manage vehicle usage',
            'view reports',
            'view dashboard'
        ]);

        $karyawanRole->givePermissionTo([
            'request item',
            'request vehicle'
        ]);

        // Create users
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password123')
        ]);
        $superAdmin->assignRole('super admin');

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123')
        ]);
        $admin->assignRole('admin');

        $karyawan = User::create([
            'name' => 'Karyawan',
            'email' => 'karyawan@example.com',
            'password' => bcrypt('password123')
        ]);
        $karyawan->assignRole('karyawan');
    }
}