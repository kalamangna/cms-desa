<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_are_created_by_seeder(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->assertDatabaseHas('roles', ['name' => 'super_admin']);
        $this->assertDatabaseHas('roles', ['name' => 'admin_desa']);
        $this->assertDatabaseHas('roles', ['name' => 'agen_statistik']);
    }

    public function test_super_admin_user_has_super_admin_role(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $user = User::where('email', 'admin@desa.id')->first();
        $this->assertTrue($user->hasRole('super_admin'));
    }
}
