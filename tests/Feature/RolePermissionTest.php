<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DefaultDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_are_created_by_seeder(): void
    {
        $this->seed(DefaultDataSeeder::class);

        $this->assertDatabaseHas('roles', ['name' => 'super_admin']);
        $this->assertDatabaseHas('roles', ['name' => 'admin_desa']);
    }

    public function test_super_admin_user_has_super_admin_role(): void
    {
        $this->seed(DefaultDataSeeder::class);

        $user = User::where('username', 'kalamangna')->first();
        $this->assertTrue($user->hasRole('super_admin'));
    }
}
