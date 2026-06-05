<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StatisticCategory;
use App\Models\StatisticIndicator;
use App\Models\StatisticData;
use Illuminate\Support\Str;

class StatisticDataSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Penduduk' => [
                'Jumlah Laki-laki' => ['unit' => 'Jiwa', 'values' => [2021 => 1650, 2022 => 1680, 2023 => 1710, 2024 => 1750]],
                'Jumlah Perempuan' => ['unit' => 'Jiwa', 'values' => [2021 => 1600, 2022 => 1630, 2023 => 1660, 2024 => 1671]],
            ],
            'Pendidikan' => [
                'SD' => ['unit' => 'Orang', 'values' => [2021 => 520, 2022 => 530, 2023 => 540, 2024 => 550]],
                'SMP' => ['unit' => 'Orang', 'values' => [2021 => 410, 2022 => 420, 2023 => 430, 2024 => 440]],
                'SMA' => ['unit' => 'Orang', 'values' => [2021 => 310, 2022 => 320, 2023 => 335, 2024 => 350]],
                'Sarjana' => ['unit' => 'Orang', 'values' => [2021 => 85, 2022 => 95, 2023 => 110, 2024 => 125]],
            ],
            'Pekerjaan' => [
                'Nelayan' => ['unit' => 'Orang', 'values' => [2024 => 650]],
                'Petani' => ['unit' => 'Orang', 'values' => [2024 => 420]],
                'PNS' => ['unit' => 'Orang', 'values' => [2024 => 65]],
                'Wiraswasta' => ['unit' => 'Orang', 'values' => [2024 => 210]],
                'Lainnya' => ['unit' => 'Orang', 'values' => [2024 => 180]],
            ],
            'Kemiskinan' => [
                'Keluarga Prasejahtera' => ['unit' => 'KK', 'values' => [2021 => 120, 2022 => 110, 2023 => 95, 2024 => 82]],
            ],
            'Stunting' => [
                'Balita Stunting' => ['unit' => 'Anak', 'values' => [2021 => 18, 2022 => 15, 2023 => 11, 2024 => 8]],
            ],
            'UMKM' => [
                'Jumlah Unit UMKM' => ['unit' => 'Unit', 'values' => [2021 => 25, 2022 => 32, 2023 => 40, 2024 => 52]],
            ],
        ];

        foreach ($data as $catName => $indicators) {
            $category = StatisticCategory::firstOrCreate(
                ['name' => $catName],
                ['slug' => Str::slug($catName)]
            );

            foreach ($indicators as $indName => $indData) {
                $indicator = StatisticIndicator::firstOrCreate(
                    [
                        'statistic_category_id' => $category->id,
                        'name' => $indName
                    ],
                    ['unit' => $indData['unit']]
                );

                foreach ($indData['values'] as $year => $value) {
                    StatisticData::updateOrCreate(
                        [
                            'statistic_indicator_id' => $indicator->id,
                            'year' => $year
                        ],
                        ['value' => $value]
                    );
                }
            }
        }
    }
}
