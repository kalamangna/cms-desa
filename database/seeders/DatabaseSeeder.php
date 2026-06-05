<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SettingSeeder::class,
            StatisticDataSeeder::class,
            SampleDataSeeder::class,
        ]);

        $admin = User::factory()->create([
            'name' => 'Admin Sinjai',
            'username' => 'admin',
            'email' => 'admin@sinjaikab.go.id',
            'password' => Hash::make('admin123'),
        ]);

        $admin->assignRole('super_admin');
    }
}
