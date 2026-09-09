<?php

namespace Database\Seeders;

use App\Models\PopupInfographic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PopupInfographicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membersihkan dan membuat data Popup Infografis...');

        // Membersihkan data lama popup
        PopupInfographic::query()->forceDelete();

        // Kategori Statistik sengaja tidak di-seed agar tetap dikelola manual/ril desa.

        // Pastikan direktori popup-infographics ada
        if (! Storage::disk('public')->exists('popup-infographics')) {
            Storage::disk('public')->makeDirectory('popup-infographics');
        }
        // Salin meta.webp sebagai dummy jika belum ada
        if (! Storage::disk('public')->exists('popup-infographics/meta.webp') && file_exists(public_path('img/meta.webp'))) {
            Storage::disk('public')->put('popup-infographics/meta.webp', file_get_contents(public_path('img/meta.webp')));
        }

        // 2. Popup Infografis
        $popups = [
            [
                'title' => 'Selamat Datang di Website Desa Tompobulu',
                'image' => 'popup-infographics/meta.webp',
                'sort_order' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($popups as $popup) {
            PopupInfographic::create($popup);
        }

        $this->command->info('Berhasil menyuntikkan 1 Popup Infografis.');
    }
}
