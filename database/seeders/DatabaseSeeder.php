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
            DefaultDataSeeder::class,
        ]);

        if (filter_var(env('SEED_SAMPLE_DATA', true), FILTER_VALIDATE_BOOLEAN)) {
            $this->call([
                SampleDataSeeder::class,
            ]);
        }
    }
}
