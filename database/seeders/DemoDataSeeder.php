<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed data contoh lengkap khusus untuk kebutuhan demo dan pengembangan lokal.
     */
    public function run(): void
    {
        config(['services.telegram-bot-api.chat_id' => null]);

        $this->call([
            DatabaseSeeder::class,
            ArticleSeeder::class,
            VillageProfileSeeder::class,
            GalleryAndPotentialSeeder::class,
            DocumentSeeder::class,
            ApbdesSeeder::class,
            PopupInfographicSeeder::class,
        ]);
    }
}
