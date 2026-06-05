<?php

namespace Tests\Feature;

use App\Models\StatisticCategory;
use App\Models\StatisticIndicator;
use App\Models\StatisticData;
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
}
