<?php

namespace App\Services;

use App\Models\Citizen;
use App\Models\Dataset;
use App\Models\Dusun;
use App\Models\Family;
use App\Models\StatisticCategory;
use App\Models\StatisticData;
use App\Models\StatisticIndicator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatisticService
{
    const ALLOWED_OPERATORS = ['=', '!=', '>', '<', '>=', '<=', 'LIKE', 'whereNotNull', 'whereNullOrTidak', 'whereNotIn'];

    /**
     * Bersihkan seluruh cache statistik data.
     */
    public static function clearCache(): void
    {
        try {
            if (Cache::has('statistic_data_version')) {
                Cache::increment('statistic_data_version');
            } else {
                Cache::put('statistic_data_version', 2, 86400 * 30);
            }
        } catch (\Throwable $e) {
        }
    }

    /**
     * Ambil data statistik lengkap untuk halaman /statistik.
     */
    public function getStatisticsData(
        ?int $selectedDusunId,
        ?int $selectedYear,
    ): array {
        $version = Cache::get('statistic_data_version', 1);
        $cacheKey = "statistic_data_v{$version}_{$selectedDusunId}_{$selectedYear}";

        return Cache::remember($cacheKey, 3600, function () use ($selectedDusunId, $selectedYear) {
            $currentYear = (int) date('Y');
            $isEmptyDb = $this->isDatabaseEmpty();

            // Muat daftar dusun beserta hitungan penduduk aktif dan keluarga sekali saja untuk seluruh agregasi
            $dusuns = Dusun::withCount([
                'citizens' => fn ($q) => $q->where('status', 'Aktif'),
                'families',
            ])->orderBy('name', 'asc')->get();

            $categories = $this->loadCategoriesWithLiveData(
                $currentYear,
                $isEmptyDb,
                $selectedDusunId,
                $selectedYear,
                $dusuns,
            );

            return [
                'isEmptyDb' => $isEmptyDb,
                'categories' => $categories,
                'summaryCards' => $this->buildSummaryCards($currentYear, $isEmptyDb, $dusuns),
                'availableYears' => $this->getAvailableYears($currentYear),
                'dusuns' => $dusuns,
                'datasets' => Dataset::latest()->take(3)->get(),
            ];
        });
    }

    /**
     * Format data untuk JSON API response.
     */
    public function formatJsonResponse(Collection $categories): array
    {
        $data = [];

        foreach ($categories as $category) {
            $indicatorsData = [];
            $palette = ['#10b981', '#0ea5e9', '#f59e0b', '#8b5cf6', '#ec4899', '#f43f5e', '#06b6d4', '#14b8a6', '#f97316', '#3b82f6'];
            foreach ($category->indicators as $idx => $indicator) {
                $c = $palette[$idx % count($palette)];
                if (str_contains(strtolower($indicator->name), 'laki-laki') || str_contains(strtolower($indicator->name), 'laki laki')) {
                    $c = '#0ea5e9';
                } elseif (str_contains(strtolower($indicator->name), 'perempuan')) {
                    $c = '#ec4899';
                }
                $indicatorsData[] = [
                    'name' => $indicator->name,
                    'unit' => $indicator->unit,
                    'color' => $c,
                    'breakdowns' => $indicator->breakdowns ?? [],
                    'data' => $indicator->data->map(fn ($d) => [
                        'year' => (int) $d->year,
                        'value' => (int) $d->value,
                        'value_male' => (int) ($d->value_male ?? $d->value),
                        'value_female' => (int) ($d->value_female ?? 0),
                    ])->toArray(),
                ];
            }

            $data[$category->slug] = [
                'name' => $category->name,
                'secondaryConfigs' => $category->secondaryConfigs ?? [],
                'years' => $category->indicators
                    ->flatMap(fn ($i) => $i->data ? $i->data->pluck('year') : collect())
                    ->unique()->sort()->values()->toArray(),
                'indicators' => $indicatorsData,
            ];
        }

        return $data;
    }

    // ─── PRIVATE METHODS ────────────────────────────────────────────────

    private function isDatabaseEmpty(): bool
    {
        return Citizen::count() === 0 && Family::count() === 0;
    }

    private function getAvailableYears(int $currentYear): array
    {
        return StatisticData::distinct()
            ->pluck('year')
            ->push($currentYear)
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    private function buildSummaryCards(int $currentYear, bool $isEmptyDb, Collection $dusuns): array
    {
        $totalPendudukNow = $isEmptyDb
            ? 0
            : Citizen::where('status', 'Aktif')->count();

        $yoyGrowth = $this->calculateYoYGrowth($currentYear, $totalPendudukNow);

        $topDusun = $isEmptyDb ? null : $dusuns->sortByDesc('citizens_count')->first();

        return [
            'total_penduduk' => $totalPendudukNow,
            'yoy_growth' => $yoyGrowth,
            'latest_year' => $currentYear,
            'top_dusun' => $topDusun?->name ?? '-',
            'top_dusun_count' => $topDusun?->citizens_count ?? 0,
        ];
    }

    private function calculateYoYGrowth(int $currentYear, int $citizenCountThisYear): ?float
    {
        $citizenCountLastYear = StatisticData::where('year', $currentYear - 1)
            ->whereHas('indicator', fn ($q) => $q->where('name', 'Total Penduduk'))
            ->value('value');

        if (! $citizenCountLastYear || $citizenCountLastYear <= 0) {
            return null;
        }

        return round(
            (($citizenCountThisYear - $citizenCountLastYear) / $citizenCountLastYear) * 100,
            2,
        );
    }

    private function loadCategoriesWithLiveData(
        int $currentYear,
        bool $isEmptyDb,
        ?int $selectedDusunId,
        ?int $selectedYear,
        Collection $dusuns,
    ): Collection {
        $categories = StatisticCategory::where('is_active', true)
            ->with(['indicators.data' => fn ($q) => $q->orderBy('year', 'asc')])
            ->get();

        $dusunCategory = $this->createStaticDusunCategory($currentYear, $selectedDusunId, $dusuns);

        if ($categories->isEmpty()) {
            $categories = $dusunCategory;
        } else {
            $categories = $dusunCategory->concat($categories);
        }

        foreach ($categories as $category) {
            if (! $category->mapping_table) {
                continue;
            }
            if (! in_array($category->mapping_table, StatisticCategory::ALLOWED_TABLES, true)) {
                continue;
            }

            $secondaryConfigs = $this->getSecondaryOptions($category, $dusuns);
            $category->secondaryConfigs = $secondaryConfigs;

            $allowedColumns = StatisticCategory::ALLOWED_COLUMNS[$category->mapping_table] ?? [];

            foreach ($category->indicators as $indicator) {
                if (
                    ! $indicator->mapping_column
                    || ! in_array($indicator->mapping_column, $allowedColumns, true)
                ) {
                    continue;
                }

                $counts = $this->queryIndicatorSummary($category, $indicator, $selectedDusunId, $isEmptyDb);
                $liveValue = $counts['total'];
                $genderBreakdown = [
                    'male' => $counts['male'],
                    'female' => $counts['female'],
                ];

                // Hitung rincian untuk setiap kolom sekunder memakai agregasi GROUP BY
                $breakdowns = [];
                foreach ($secondaryConfigs as $secKey => $secConf) {
                    $breakdowns[$secKey] = $this->getSecondaryBreakdownCounts(
                        $category,
                        $indicator,
                        $secKey,
                        $secConf['options'],
                        $selectedDusunId,
                        $isEmptyDb,
                        $dusuns,
                        $genderBreakdown,
                    );
                }
                $indicator->breakdowns = $breakdowns;

                $liveData = new StatisticData([
                    'year' => $currentYear,
                    'value' => $liveValue,
                    'value_male' => $genderBreakdown['male'],
                    'value_female' => $genderBreakdown['female'],
                ]);

                $mergedData = $this->mergeHistoricalWithLive(
                    $indicator,
                    $liveData,
                    $currentYear,
                    $isEmptyDb,
                    $selectedDusunId,
                    $selectedYear,
                );

                $indicator->setRelation('data', $mergedData);
            }
        }

        return $categories;
    }

    public function getSecondaryOptions(StatisticCategory $category, ?Collection $dusuns = null): array
    {
        $secCols = $category->secondary_columns;
        if (empty($secCols)) {
            if ($category->mapping_table === 'citizens') {
                $secCols = ['gender'];
            } else {
                $secCols = [];
            }
        }

        $allLabels = [
            'gender' => 'Jenis Kelamin',
            'education_level' => 'Tingkat Pendidikan',
            'marital_status' => 'Status Perkawinan',
            'job_status' => 'Status Pekerjaan',
            'dusun_id' => 'Dusun',
            'school_participation' => 'Partisipasi Sekolah',
            'has_digital_wallet' => 'Dompet Digital / Rekening',
            'bpjs_status' => 'Kepesertaan BPJS',
            'ownership_status' => 'Status Kepemilikan Rumah',
            'building_type' => 'Jenis Bangunan',
            'water_source' => 'Sumber Air Minum',
            'lighting_source' => 'Sumber Penerangan',
        ];

        $palette = ['#0ea5e9', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6', '#f43f5e', '#06b6d4', '#14b8a6', '#f97316', '#3b82f6', '#64748b', '#84cc16'];

        $result = [];
        foreach ($secCols as $col) {
            $label = $allLabels[$col] ?? ucwords(str_replace('_', ' ', $col));
            $options = $this->getSecondaryOptionsList($category, $col, $dusuns);
            $colors = [];
            foreach ($options as $idx => $opt) {
                if ($col === 'gender') {
                    $colors[] = str_contains(strtolower($opt), 'laki') ? '#0ea5e9' : '#ec4899';
                } else {
                    $colors[] = $palette[$idx % count($palette)];
                }
            }
            $result[$col] = [
                'key' => $col,
                'label' => $label,
                'options' => $options,
                'colors' => $colors,
            ];
        }

        return $result;
    }

    private function getSecondaryOptionsList(StatisticCategory $category, string $column, ?Collection $dusuns = null): array
    {
        return match ($column) {
            'gender' => ['Laki-laki', 'Perempuan'],
            'education_level' => [
                'Tidak Punya Ijazah SD', 'SD / Sederajat', 'SMP / Sederajat',
                'SMA / Sederajat', 'D1 / D2 / D3', 'D4 / S1 / Profesi', 'S2 / S3',
            ],
            'marital_status' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'],
            'job_status' => [
                'Berusaha Sendiri', 'Buruh / Karyawan / Pegawai Swasta', 'Pekerja Bebas',
                'Pekerja Keluarga / Tidak Dibayar', 'ASN / TNI / Polri / BUMN / BUMD / Pejabat Negara',
                'Berusaha Dibantu Buruh', 'Lainnya',
            ],
            'school_participation' => ['Tidak / Belum Pernah Sekolah', 'Masih Sekolah', 'Tidak Bersekolah Lagi'],
            'has_digital_wallet' => ['Tidak Ada', 'Ya untuk Pribadi', 'Ya untuk Usaha & Pribadi', 'Ya untuk Usaha'],
            'bpjs_status' => ['BPJS PBI Pemda', 'BPJS Mandiri', 'BPJS PBI Tunjangan Pemerintah Pusat', 'Tidak Terdaftar'],
            'ownership_status' => ['Milik Sendiri', 'Bebas Sewa', 'Sewa / Kontrak'],
            'building_type' => ['Rumah Tinggal Tunggal', 'Lainnya'],
            'water_source' => ['Sumur Terlindung', 'Sumur Bor / Pompa', 'Leding', 'Mata Air', 'Air kemasan bermerek', 'Lainnya'],
            'lighting_source' => ['Listrik PLN Dengan Meteran', 'Listrik PLN Tanpa Meteran', 'Listrik Non-PLN', 'Bukan Listrik'],
            'dusun_id' => ($dusuns ?? Dusun::orderBy('name', 'asc')->get())->pluck('name')->map(fn ($n) => 'Dusun '.$n)->toArray(),
            default => DB::table($category->mapping_table)->whereNotNull($column)->where($column, '!=', '')->distinct()->pluck($column)->toArray(),
        };
    }

    private function getSecondaryBreakdownCounts(
        StatisticCategory $category,
        StatisticIndicator $indicator,
        string $secColumn,
        array $secOptions,
        ?int $selectedDusunId,
        bool $isEmptyDb,
        Collection $dusuns,
        array $genderBreakdown,
    ): array {
        if ($isEmptyDb) {
            $res = [];
            foreach ($secOptions as $opt) {
                $res[$opt] = 0;
            }

            return $res;
        }

        // Gunakan kembali hasil agregasi gender dari query utama indikator agar tidak query ulang
        if ($secColumn === 'gender' && $category->mapping_table === 'citizens') {
            return [
                'Laki-laki' => $genderBreakdown['male'],
                'Perempuan' => $genderBreakdown['female'],
            ];
        }

        $baseQuery = $category->mapping_table === 'families'
            ? Family::query()
            : Citizen::query()->where('status', 'Aktif');

        if ($selectedDusunId) {
            $baseQuery->where('dusun_id', $selectedDusunId);
        }

        $this->applyIndicatorCondition($baseQuery, $indicator);

        $res = [];
        if ($secColumn === 'dusun_id') {
            $grouped = $baseQuery
                ->selectRaw('dusun_id, COUNT(*) as total')
                ->groupBy('dusun_id')
                ->pluck('total', 'dusun_id');

            foreach ($dusuns as $dusun) {
                $res['Dusun '.$dusun->name] = (int) ($grouped[$dusun->id] ?? 0);
            }
        } elseif ($secColumn === 'assistance_type') {
            foreach ($secOptions as $opt) {
                $q = clone $baseQuery;
                if ($opt === 'Tidak Menerima Bantuan') {
                    $q->where(fn ($sub) => $sub->whereNull('assistance_type')->orWhere('assistance_type', 'Tidak Ada')->orWhere('assistance_type', ''));
                } else {
                    $q->where('assistance_type', 'LIKE', '%'.$opt.'%');
                }
                $res[$opt] = $q->count();
            }
        } else {
            $rows = $baseQuery
                ->select($secColumn, DB::raw('COUNT(*) as total'))
                ->groupBy($secColumn)
                ->get();

            $countsByVal = [];
            $emptyCount = 0;
            foreach ($rows as $row) {
                $val = $row->{$secColumn};
                if ($val === null || $val === '') {
                    $emptyCount += (int) $row->total;
                } else {
                    $countsByVal[$val] = (int) $row->total;
                }
            }

            foreach ($secOptions as $opt) {
                if ($opt === 'Tidak Ada' || $opt === 'Tidak Terdaftar') {
                    $res[$opt] = ($countsByVal[$opt] ?? 0) + $emptyCount;
                } else {
                    $res[$opt] = $countsByVal[$opt] ?? 0;
                }
            }
        }

        return $res;
    }

    /**
     * Hitung total indikator beserta rincian gender dalam satu query agregasi.
     *
     * @return array{total: int, male: int, female: int}
     */
    private function queryIndicatorSummary(
        StatisticCategory $category,
        StatisticIndicator $indicator,
        ?int $selectedDusunId,
        bool $isEmptyDb,
    ): array {
        if ($isEmptyDb) {
            return ['total' => 0, 'male' => 0, 'female' => 0];
        }

        $query = $category->mapping_table === 'families'
            ? Family::query()
            : Citizen::query()->where('status', 'Aktif');

        if ($selectedDusunId) {
            $query->where('dusun_id', $selectedDusunId);
        }

        $this->applyIndicatorCondition($query, $indicator);

        if ($category->mapping_table === 'citizens') {
            $agg = $query
                ->selectRaw("COUNT(*) as total, COALESCE(SUM(CASE WHEN gender = 'Laki-laki' THEN 1 ELSE 0 END), 0) as male, COALESCE(SUM(CASE WHEN gender = 'Perempuan' THEN 1 ELSE 0 END), 0) as female")
                ->first();

            return [
                'total' => (int) ($agg->total ?? 0),
                'male' => (int) ($agg->male ?? 0),
                'female' => (int) ($agg->female ?? 0),
            ];
        }

        return [
            'total' => $query->count(),
            'male' => 0,
            'female' => 0,
        ];
    }

    private function applyIndicatorCondition($query, StatisticIndicator $indicator): void
    {
        $operator = $indicator->mapping_operator ?: '=';
        $column = $indicator->mapping_column;
        $value = $indicator->mapping_value;

        if (! in_array($operator, self::ALLOWED_OPERATORS, true)) {
            $operator = '=';
        }

        match ($operator) {
            'LIKE' => $query->where($column, 'LIKE', '%'.$value.'%'),
            default => $this->applyStandardCondition($query, $column, $operator, $value),
        };
    }

    private function applyStandardCondition($query, string $column, string $operator, ?string $value): void
    {
        match ($operator) {
            'whereNotNull' => $query
                ->whereNotNull($column)
                ->where($column, '!=', '')
                ->where($column, '!=', 'Tidak Ada')
                ->where($column, '!=', 'Tidak'),
            'whereNullOrTidak' => $query->where(function ($q) use ($column) {
                $q->whereNull($column)
                    ->orWhere($column, '')
                    ->orWhere($column, 'Tidak Ada')
                    ->orWhere($column, 'Tidak');
            }),
            'whereNotIn' => $query->where(function ($q) use ($column, $value) {
                $arr = array_map('trim', explode(',', (string) $value));
                $q->whereNotIn($column, $arr)
                    ->orWhereNull($column);
            }),
            default => is_null($value)
                ? $query->whereNull($column)
                : $query->where($column, $operator, $value),
        };
    }

    private function mergeHistoricalWithLive(
        StatisticIndicator $indicator,
        StatisticData $liveData,
        int $currentYear,
        bool $isEmptyDb,
        ?int $selectedDusunId,
        ?int $selectedYear,
    ): Collection {
        $historicalData = $indicator->data ? $indicator->data->filter(
            fn ($d) => (int) $d->year !== $currentYear,
        ) : collect();

        if ($selectedDusunId) {
            $historicalData = collect();
        }

        if ($isEmptyDb) {
            $historicalData = $historicalData->map(function ($d) {
                $d->value = 0;

                return $d;
            });
        }

        $merged = $historicalData->push($liveData)->sortBy('year')->values();

        if ($selectedYear) {
            $merged = $merged->filter(
                fn ($d) => (int) $d->year === (int) $selectedYear,
            )->values();
        }

        return $merged;
    }

    private function createStaticDusunCategory(int $currentYear, ?int $selectedDusunId, Collection $dusuns): Collection
    {
        $grouped = Citizen::where('status', 'Aktif')
            ->when($selectedDusunId, fn ($q) => $q->where('dusun_id', $selectedDusunId))
            ->selectRaw('dusun_id, gender, COUNT(*) as total')
            ->groupBy('dusun_id', 'gender')
            ->get();

        $countsByDusun = [];
        foreach ($grouped as $row) {
            $countsByDusun[$row->dusun_id][$row->gender] = (int) $row->total;
        }

        $indicators = collect();

        foreach ($dusuns as $dusun) {
            if ($selectedDusunId && $dusun->id != $selectedDusunId) {
                continue;
            }

            $male = $countsByDusun[$dusun->id]['Laki-laki'] ?? 0;
            $female = $countsByDusun[$dusun->id]['Perempuan'] ?? 0;
            $total = $male + $female;

            $liveData = new StatisticData([
                'year' => $currentYear,
                'value' => $total,
                'value_male' => $male,
                'value_female' => $female,
            ]);

            $indicator = new StatisticIndicator([
                'name' => 'Dusun '.$dusun->name,
                'unit' => 'Jiwa',
            ]);
            $indicator->setRelation('data', collect([$liveData]));

            $indicator->breakdowns = [
                'gender' => [
                    'Laki-laki' => $male,
                    'Perempuan' => $female,
                ],
            ];

            $indicators->push($indicator);
        }

        $category = new StatisticCategory([
            'name' => 'Penduduk',
            'slug' => 'penduduk',
            'description' => 'Data statistik real-time sebaran jumlah penduduk aktif berdasarkan wilayah dusun.',
            'mapping_table' => 'citizens',
            'secondary_columns' => ['gender'],
            'is_active' => true,
        ]);

        $category->secondaryConfigs = [
            'gender' => [
                'key' => 'gender',
                'label' => 'Jenis Kelamin',
                'options' => ['Laki-laki', 'Perempuan'],
                'colors' => ['#0ea5e9', '#ec4899'],
            ],
        ];

        $category->setRelation('indicators', $indicators);

        return collect([$category]);
    }
}
