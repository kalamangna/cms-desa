<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Roles & Permissions Setup
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'admin_desa']);
        Role::firstOrCreate(['name' => 'agen_statistik']);

        $user = User::firstOrCreate(
            ['username' => 'kalamangna'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Syazani'),
            ]
        );

        $user->assignRole($superAdminRole);
    }
}
