<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'village_name', 'value' => 'Tompobulu'],
            ['key' => 'village_logo', 'value' => null],
            ['key' => 'village_head', 'value' => 'Asri S.'],
            ['key' => 'village_email', 'value' => 'desa.tompobulu@sinjaikab.go.id'],
            ['key' => 'village_phone', 'value' => '081244556677'],
            ['key' => 'village_address', 'value' => 'Jl. Poros Bulupoddo, Desa Tompobulu'],
            ['key' => 'district_name', 'value' => 'Bulupoddo'],
            ['key' => 'regency_name', 'value' => 'Kabupaten Sinjai'],
            ['key' => 'province_name', 'value' => 'Sulawesi Selatan'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }
}
