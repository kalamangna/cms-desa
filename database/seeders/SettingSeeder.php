<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\LetterNumberService;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Menyiapkan pengaturan dasar netral untuk website desa baru...');

        $settings = [
            // Wilayah administratif dasar
            ['key' => 'province_name', 'value' => 'Sulawesi Selatan'],
            ['key' => 'regency_name', 'value' => 'Kabupaten Sinjai'],
            ['key' => 'village_area_unit', 'value' => 'Hektar'],

            // Tampilan dan aksesibilitas bawaan
            ['key' => 'primary_color', 'value' => '#10b981'],
            ['key' => 'enable_a11y_widget', 'value' => '1'],
            ['key' => 'a11y_widget_position', 'value' => 'bottom-left'],

            // Pengaturan awal surat dan TTE
            ['key' => 'tte_enabled', 'value' => '0'],
            ['key' => 'tte_village_code', 'value' => 'DESA'],
            ['key' => 'tte_letter_number_format', 'value' => LetterNumberService::DEFAULT_FORMAT],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        $this->command->info('Berhasil menyiapkan '.count($settings).' pengaturan dasar website desa.');
    }
}
