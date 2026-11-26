<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed data inti produksi yang bersih dan netral untuk instalasi website desa baru.
     */
    public function run(): void
    {
        // Cegah pengiriman notifikasi Telegram selama proses seeding database
        config(['services.telegram-bot-api.chat_id' => null]);

        $this->call([
            DefaultDataSeeder::class,
            SettingSeeder::class,
            MasterCategorySeeder::class,
            ServiceSeeder::class,
        ]);
    }
}
