<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Setting;
use App\Models\StatisticCategory;
use App\Models\StatisticIndicator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions Setup
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'admin_desa']);
        Role::firstOrCreate(['name' => 'agen_statistik']);

        $user = User::firstOrCreate(
            ['username' => 'kalamangna'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Syazani'),
            ]
        );

        $user->assignRole($superAdminRole);

        // 2. Default Settings Setup
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

        // 3. Statistic Categories & Indicators Setup
        $statistics = [
            'Penduduk' => [
                'Jumlah Laki-laki' => 'Jiwa',
                'Jumlah Perempuan' => 'Jiwa',
            ],
            'Pendidikan' => [
                'SD' => 'Orang',
                'SMP' => 'Orang',
                'SMA' => 'Orang',
                'Sarjana' => 'Orang',
            ],
            'Pekerjaan' => [
                'Nelayan' => 'Orang',
                'Petani' => 'Orang',
                'PNS' => 'Orang',
                'Wiraswasta' => 'Orang',
                'Lainnya' => 'Orang',
            ],
            'Kemiskinan' => [
                'Keluarga Prasejahtera' => 'KK',
            ],
            'Stunting' => [
                'Balita Stunting' => 'Anak',
            ],
            'UMKM' => [
                'Jumlah Unit UMKM' => 'Unit',
            ],
        ];

        foreach ($statistics as $catName => $indicators) {
            $category = StatisticCategory::firstOrCreate(
                ['name' => $catName],
                ['slug' => Str::slug($catName)]
            );

            foreach ($indicators as $indName => $unit) {
                StatisticIndicator::firstOrCreate(
                    [
                        'statistic_category_id' => $category->id,
                        'name' => $indName
                    ],
                    ['unit' => $unit]
                );
            }
        }
    }
}
