<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
