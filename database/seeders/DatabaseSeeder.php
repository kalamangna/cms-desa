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
        // Cegah pengiriman notifikasi Telegram selama proses seeding database
        config(['services.telegram-bot-api.chat_id' => null]);

        $this->call([
            DefaultDataSeeder::class,
            SettingSeeder::class,
            ArticleSeeder::class,
            VillageProfileSeeder::class,
            GalleryAndPotentialSeeder::class,
            DocumentSeeder::class,
            ApbdesSeeder::class,
            ServiceSeeder::class,
            PopupInfographicSeeder::class,
        ]);
    }
}
