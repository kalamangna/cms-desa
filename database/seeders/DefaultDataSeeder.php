<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
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
        $adminDesaRole = Role::firstOrCreate(['name' => 'admin_desa']);

        // Ambil semua permission yang ada
        $permissions = \Spatie\Permission\Models\Permission::all();
        
        // Berikan SEMUA izin ke super_admin
        $superAdminRole->syncPermissions($permissions);

        // Berikan izin ke admin_desa KECUALI untuk pengguna, peran, cadangan, audit log
        $adminDesaPermissions = $permissions->filter(function ($permission) {
            $excluded = ['user', 'role', 'permission', 'backup', 'audit'];
            foreach ($excluded as $exc) {
                if (stripos($permission->name, $exc) !== false) {
                    return false;
                }
            }
            return true;
        });
        $adminDesaRole->syncPermissions($adminDesaPermissions);

        $user = User::firstOrCreate(
            ['username' => 'kalamangna'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Syazani'),
            ]
        );

        $user->assignRole($superAdminRole);

        // 2. Default Theme Color Setup
        Setting::firstOrCreate(
            ['key' => 'primary_color'],
            ['value' => '#10b981']
        );
    }
}
