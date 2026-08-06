<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DefaultDataSeeder::class,
            SettingSeeder::class,
            ArticleSeeder::class,
            VillageProfileSeeder::class,
            PopulationSeeder::class,
            GalleryAndFacilitySeeder::class,
            DocumentAndPublicationSeeder::class,
            ApbdesAndDatasetSeeder::class,
            PublicServiceSeeder::class,
            StatisticAndPopupSeeder::class,
        ]);
    }
}
