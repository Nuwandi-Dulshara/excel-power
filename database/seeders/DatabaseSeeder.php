<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@pos.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        $cashier = User::firstOrCreate(
            ['email' => 'cashier@pos.com'],
            [
                'name' => 'Cashier',
                'password' => Hash::make('password'),
            ]
        );
        $cashier->assignRole('cashier');

        $developer = User::firstOrCreate(
            ['email' => 'developer@pos.com'],
            [
                'name' => 'Developer',
                'password' => Hash::make('password'),
            ]
        );
        $developer->assignRole('developer');
    }
}