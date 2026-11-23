<?php

namespace Tests\Feature;

use App\Models\Citizen;
use App\Models\Dusun;
use App\Models\Family;
use App\Models\StatisticCategory;
use App\Models\StatisticIndicator;
use App\Services\StatisticService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistic_dashboard_page_is_accessible(): void
    {
        $response = $this->get('/statistik');

        $response->assertStatus(200);
        $response->assertSee('Statistik');
        $response->assertSee('Desa');
    }

    public function test_statistic_service_aggregates_dusun_gender_and_secondary_breakdowns_accurately(): void
    {
        $dusunA = Dusun::create(['name' => 'Alpha', 'slug' => 'alpha', 'total_rt' => 2, 'total_rw' => 1]);
        $dusunB = Dusun::create(['name' => 'Beta', 'slug' => 'beta', 'total_rt' => 1, 'total_rw' => 1]);

        $familyA = Family::create(['kk_number' => '7307010101010001', 'head_name' => 'Kepala A', 'dusun_id' => $dusunA->id]);
        $familyB = Family::create(['kk_number' => '7307010101010002', 'head_name' => 'Kepala B', 'dusun_id' => $dusunB->id]);

        Citizen::create([
            'family_id' => $familyA->id,
            'dusun_id' => $dusunA->id,
            'nik' => '7307010101900001',
            'name' => 'Warga Satu',
            'gender' => 'Laki-laki',
            'status' => 'Aktif',
            'education_level' => 'SMA / Sederajat',
            'bpjs_status' => 'BPJS Mandiri',
        ]);
        Citizen::create([
            'family_id' => $familyA->id,
            'dusun_id' => $dusunA->id,
            'nik' => '7307014101920002',
            'name' => 'Warga Dua',
            'gender' => 'Perempuan',
            'status' => 'Aktif',
            'education_level' => 'SMA / Sederajat',
            'bpjs_status' => null,
        ]);
        Citizen::create([
            'family_id' => $familyB->id,
            'dusun_id' => $dusunB->id,
            'nik' => '7307010202950003',
            'name' => 'Warga Tiga',
            'gender' => 'Laki-laki',
            'status' => 'Aktif',
            'education_level' => 'SD / Sederajat',
            'bpjs_status' => 'Tidak Terdaftar',
        ]);

        $category = StatisticCategory::create([
            'name' => 'Pendidikan',
            'slug' => 'pendidikan',
            'mapping_table' => 'citizens',
            'secondary_columns' => ['gender', 'dusun_id', 'bpjs_status'],
            'is_active' => true,
        ]);

        StatisticIndicator::create([
            'statistic_category_id' => $category->id,
            'name' => 'Lulusan SMA',
            'unit' => 'Jiwa',
            'mapping_column' => 'education_level',
            'mapping_operator' => '=',
            'mapping_value' => 'SMA / Sederajat',
        ]);

        StatisticService::clearCache();
        $result = app(StatisticService::class)->getStatisticsData(null, null);

        // Ringkasan penduduk dan dusun terbanyak
        $this->assertSame(3, $result['summaryCards']['total_penduduk']);
        $this->assertSame('Alpha', $result['summaryCards']['top_dusun']);
        $this->assertSame(2, $result['summaryCards']['top_dusun_count']);

        // Kategori statis Penduduk per Dusun (GROUP BY dusun_id, gender)
        $pendudukCat = $result['categories']->firstWhere('slug', 'penduduk');
        $dusunAIndicator = $pendudukCat->indicators->firstWhere('name', 'Dusun Alpha');
        $this->assertSame(2, $dusunAIndicator->data->first()->value);
        $this->assertSame(1, $dusunAIndicator->data->first()->value_male);
        $this->assertSame(1, $dusunAIndicator->data->first()->value_female);

        // Kategori Pendidikan dengan breakdown gender, dusun_id, dan bpjs_status (termasuk null → Tidak Terdaftar)
        $eduCat = $result['categories']->firstWhere('slug', 'pendidikan');
        $smaIndicator = $eduCat->indicators->firstWhere('name', 'Lulusan SMA');
        $this->assertSame(2, $smaIndicator->data->first()->value);
        $this->assertSame(1, $smaIndicator->breakdowns['gender']['Laki-laki']);
        $this->assertSame(1, $smaIndicator->breakdowns['gender']['Perempuan']);
        $this->assertSame(2, $smaIndicator->breakdowns['dusun_id']['Dusun Alpha']);
        $this->assertSame(0, $smaIndicator->breakdowns['dusun_id']['Dusun Beta']);
        $this->assertSame(1, $smaIndicator->breakdowns['bpjs_status']['BPJS Mandiri']);
        $this->assertSame(1, $smaIndicator->breakdowns['bpjs_status']['Tidak Terdaftar']);
    }
}
