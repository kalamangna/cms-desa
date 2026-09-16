<?php

namespace Tests\Feature;

use App\Models\Citizen;
use App\Models\Family;
use App\Models\StatisticCategory;
use App\Models\StatisticData;
use App\Models\StatisticIndicator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_statistic_category(): void
    {
        StatisticCategory::create([
            'name' => 'Penduduk',
            'slug' => 'penduduk',
        ]);

        $this->assertDatabaseHas('statistic_categories', ['name' => 'Penduduk']);
    }

    public function test_can_create_statistic_indicator(): void
    {
        $category = StatisticCategory::create([
            'name' => 'Penduduk',
            'slug' => 'penduduk',
        ]);

        StatisticIndicator::create([
            'statistic_category_id' => $category->id,
            'name' => 'Jumlah Laki-laki',
            'unit' => 'Jiwa',
        ]);

        $this->assertDatabaseHas('statistic_indicators', ['name' => 'Jumlah Laki-laki']);
    }

    public function test_can_create_statistic_data(): void
    {
        $category = StatisticCategory::create([
            'name' => 'Penduduk',
            'slug' => 'penduduk',
        ]);

        $indicator = StatisticIndicator::create([
            'statistic_category_id' => $category->id,
            'name' => 'Jumlah Laki-laki',
            'unit' => 'Jiwa',
        ]);

        StatisticData::create([
            'statistic_indicator_id' => $indicator->id,
            'year' => 2024,
            'value' => 1500,
        ]);

        $this->assertDatabaseHas('statistic_data', [
            'year' => 2024,
            'value' => 1500,
        ]);
    }

    public function test_get_column_data_counts(): void
    {
        Citizen::create([
            'nik' => '7301010101010001',
            'name' => 'Warga 1',
            'gender' => 'Laki-laki',
            'status' => 'Aktif',
            'disability_physical' => true,
        ]);

        Citizen::create([
            'nik' => '7301010101010002',
            'name' => 'Warga 2',
            'gender' => 'Perempuan',
            'status' => 'Aktif',
            'disability_physical' => false,
        ]);

        $citizenCounts = StatisticCategory::getColumnDataCounts('citizens');
        $this->assertEquals(2, $citizenCounts['gender']);
        $this->assertEquals(1, $citizenCounts['disability_physical']);
        $this->assertEquals(0, $citizenCounts['job']);

        Family::create([
            'kk_number' => '7301010101010001',
            'head_name' => 'Kepala Keluarga 1',
            'assistance_type' => 'PKH',
        ]);

        $familyCounts = StatisticCategory::getColumnDataCounts('families');
        $this->assertEquals(1, $familyCounts['assistance_type']);
        $this->assertEquals(0, $familyCounts['water_source']);
    }
}
