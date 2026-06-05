<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminDesaRole = Role::firstOrCreate(['name' => 'admin_desa']);
        $agenStatistikRole = Role::firstOrCreate(['name' => 'agen_statistik']);

        // Create a Super Admin user if not exists
        $user = User::firstOrCreate(
            ['username' => 'kalamangna'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Syazani'),
            ]
        );

        $user->assignRole($superAdminRole);

        // Permissions for STEP 2 (Placeholders for now as resources are not yet created)
        // We will add specific permissions here as we build the modules in subsequent steps.
        // For now, we ensure the roles exist.
    }
}
