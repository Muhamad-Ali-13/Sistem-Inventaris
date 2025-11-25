<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
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

        // create roles and assign created permissions

        $superAdmin = Role::create(['name' => 'super admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
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

        $karyawan = Role::create(['name' => 'karyawan']);
        $karyawan->givePermissionTo([
            'request item',
            'request vehicle'
        ]);
    }
}