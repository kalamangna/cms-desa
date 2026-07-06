<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use App\Filament\Pages\Auth\Login;

class FilamentAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_admin_login_page_is_accessible(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    public function test_can_login_with_username(): void
    {
        $user = User::factory()->create([
            'username' => 'kalamangna',
            'password' => Hash::make('Syazani'),
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'login' => 'kalamangna',
                'password' => 'Syazani',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
    }



    public function test_home_page_is_accessible(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
