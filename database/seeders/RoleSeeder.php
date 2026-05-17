<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissionNames = [];

        foreach (Config::get('admin_permissions', []) as $group) {
            foreach ($group['items'] as $item) {
                $permissionNames[] = $item['permission'];
            }
        }

        foreach ($permissionNames as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $admin->update([
            'status' => 'active',
        ]);

        $admin->syncPermissions($permissionNames);

        $cashier = Role::firstOrCreate([
            'name' => 'cashier',
            'guard_name' => 'web',
        ]);

        $cashier->update([
            'status' => 'active',
        ]);

        $cashier->syncPermissions([
            'sales.access',
            'stocks.access',
        ]);

        $developer = Role::firstOrCreate([
            'name' => 'developer',
            'guard_name' => 'web',
        ]);

        $developer->update([
            'status' => 'active',
        ]);

        $developer->syncPermissions($permissionNames);
    }
}
