<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'KCODE Admin',
                'password' => Hash::make('password'),
                'status'   => 'active',
            ]
        );

        $superAdminRole = Role::findOrCreate('super_admin', 'web');
        $admin->assignRole($superAdminRole);
    }
}
