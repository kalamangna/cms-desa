<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'village_name', 'value' => 'Nama Desa'],
            ['key' => 'village_logo', 'value' => null],
            ['key' => 'village_head', 'value' => 'Nama Kepala Desa'],
            ['key' => 'village_email', 'value' => 'email@desa.go.id'],
            ['key' => 'village_phone', 'value' => '08123456789'],
            ['key' => 'village_address', 'value' => 'Alamat Kantor Desa'],
            ['key' => 'district_name', 'value' => 'Nama Kecamatan'],
            ['key' => 'regency_name', 'value' => 'Nama Kabupaten'],
            ['key' => 'province_name', 'value' => 'Nama Provinsi'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }
}
