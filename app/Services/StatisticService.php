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
use Illuminate\Support\Facades\DB;

class StatisticService
{
    const ALLOWED_OPERATORS = ['=', '!=', '>', '<', '>=', '<=', 'LIKE', 'whereNotNull', 'whereNullOrTidak', 'whereNotIn'];

    /**
     * Ambil data statistik lengkap untuk halaman /statistik.
     */
    public function getStatisticsData(
        ?int $selectedDusunId,
        ?int $selectedYear,
    ): array {
        $currentYear = (int) date('Y');
        $isEmptyDb = $this->isDatabaseEmpty();

        $categories = $this->loadCategoriesWithLiveData(
            $currentYear,
            $isEmptyDb,
            $selectedDusunId,
            $selectedYear,
        );

        return [
            'isEmptyDb' => $isEmptyDb,
            'categories' => $categories,
            'summaryCards' => $this->buildSummaryCards($currentYear, $isEmptyDb, $selectedDusunId),
            'availableYears' => $this->getAvailableYears($currentYear),
            'dusuns' => Dusun::withCount([
                'citizens' => fn ($q) => $q->where('status', 'Aktif'),
                'families',
            ])->orderBy('name', 'asc')->get(),
            'datasets' => Dataset::latest()->take(3)->get(),
        ];
    }

    /**
     * Format data untuk JSON API response.
     */
    public function formatJsonResponse(Collection $categories): array
    {
        $data = [];

        foreach ($categories as $category) {
            $indicatorsData = [];
            $palette = ['#10b981','#0ea5e9','#f59e0b','#8b5cf6','#ec4899','#f43f5e','#06b6d4','#14b8a6','#f97316','#3b82f6'];
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
        return StatisticData::pluck('year')
            ->push($currentYear)
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    private function buildSummaryCards(int $currentYear, bool $isEmptyDb, ?int $selectedDusunId): array
    {
        $totalPendudukNow = $isEmptyDb
            ? 0
            : Citizen::where('status', 'Aktif')->count();

        $yoyGrowth = $this->calculateYoYGrowth($currentYear, $totalPendudukNow);

        $topDusun = null;
        if (! $isEmptyDb) {
            $topDusun = Dusun::withCount(['citizens' => fn ($q) => $q->where('status', 'Aktif')])
                ->orderByDesc('citizens_count')
                ->first();
        }

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
    ): Collection {
        $categories = StatisticCategory::where('is_active', true)
            ->with(['indicators.data' => fn ($q) => $q->orderBy('year', 'asc')])
            ->get();

        $dusunCategory = $this->createStaticDusunCategory($currentYear, $selectedDusunId);

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

            $secondaryConfigs = $this->getSecondaryOptions($category);
            $category->secondaryConfigs = $secondaryConfigs;

            $allowedColumns = StatisticCategory::ALLOWED_COLUMNS[$category->mapping_table] ?? [];

            foreach ($category->indicators as $indicator) {
                if (
                    ! $indicator->mapping_column
                    || ! in_array($indicator->mapping_column, $allowedColumns, true)
                ) {
                    continue;
                }

                $liveValue = $isEmptyDb
                    ? 0
                    : $this->queryIndicatorCount($category, $indicator, $selectedDusunId);

                $genderBreakdown = $this->getGenderBreakdown($category, $indicator, $selectedDusunId, $isEmptyDb);

                // Compute breakdowns for all configured secondary columns
                $breakdowns = [];
                foreach ($secondaryConfigs as $secKey => $secConf) {
                    $breakdowns[$secKey] = $this->getSecondaryBreakdownCounts(
                        $category,
                        $indicator,
                        $secKey,
                        $secConf['options'],
                        $selectedDusunId,
                        $isEmptyDb,
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

    public function getSecondaryOptions(StatisticCategory $category): array
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

        $palette = ['#0ea5e9','#ec4899','#10b981','#f59e0b','#8b5cf6','#f43f5e','#06b6d4','#14b8a6','#f97316','#3b82f6','#64748b','#84cc16'];

        $result = [];
        foreach ($secCols as $col) {
            $label = $allLabels[$col] ?? ucwords(str_replace('_', ' ', $col));
            $options = $this->getSecondaryOptionsList($category, $col);
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

    private function getSecondaryOptionsList(StatisticCategory $category, string $column): array
    {
        return match ($column) {
            'gender' => ['Laki-laki', 'Perempuan'],
            'education_level' => [
                'Tidak Punya Ijazah SD', 'SD / Sederajat', 'SMP / Sederajat',
                'SMA / Sederajat', 'D1 / D2 / D3', 'D4 / S1 / Profesi', 'S2 / S3'
            ],
            'marital_status' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'],
            'job_status' => [
                'Berusaha Sendiri', 'Buruh / Karyawan / Pegawai Swasta', 'Pekerja Bebas',
                'Pekerja Keluarga / Tidak Dibayar', 'ASN / TNI / Polri / BUMN / BUMD / Pejabat Negara',
                'Berusaha Dibantu Buruh', 'Lainnya'
            ],
            'school_participation' => ['Tidak / Belum Pernah Sekolah', 'Masih Sekolah', 'Tidak Bersekolah Lagi'],
            'has_digital_wallet' => ['Tidak Ada', 'Ya untuk Pribadi', 'Ya untuk Usaha & Pribadi', 'Ya untuk Usaha'],
            'bpjs_status' => ['BPJS PBI Pemda', 'BPJS Mandiri', 'BPJS PBI Tunjangan Pemerintah Pusat', 'Tidak Terdaftar'],
            'ownership_status' => ['Milik Sendiri', 'Bebas Sewa', 'Sewa / Kontrak'],
            'building_type' => ['Rumah Tinggal Tunggal', 'Lainnya'],
            'water_source' => ['Sumur Terlindung', 'Sumur Bor / Pompa', 'Leding', 'Mata Air', 'Air kemasan bermerek', 'Lainnya'],
            'lighting_source' => ['Listrik PLN Dengan Meteran', 'Listrik PLN Tanpa Meteran', 'Listrik Non-PLN', 'Bukan Listrik'],
            'dusun_id' => Dusun::orderBy('name', 'asc')->pluck('name')->map(fn($n) => 'Dusun ' . $n)->toArray(),
            default => DB::table($category->mapping_table)->whereNotNull($column)->where($column, '!=', '')->distinct()->pluck($column)->toArray(),
        };
    }

    private function getSecondaryBreakdownCounts(
        StatisticCategory $category,
        StatisticIndicator $indicator,
        string $secColumn,
        array $secOptions,
        ?int $selectedDusunId,
        bool $isEmptyDb
    ): array {
        if ($isEmptyDb) {
            $res = [];
            foreach ($secOptions as $opt) {
                $res[$opt] = 0;
            }
            return $res;
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
            $dusuns = Dusun::orderBy('name', 'asc')->get();
            foreach ($dusuns as $dusun) {
                $optName = 'Dusun ' . $dusun->name;
                $res[$optName] = (clone $baseQuery)->where('dusun_id', $dusun->id)->count();
            }
        } elseif (in_array($secColumn, ['assistance_type'], true)) {
            foreach ($secOptions as $opt) {
                $q = clone $baseQuery;
                if ($opt === 'Tidak Menerima Bantuan') {
                    $q->where(fn($sub) => $sub->whereNull('assistance_type')->orWhere('assistance_type', 'Tidak Ada')->orWhere('assistance_type', ''));
                } else {
                    $q->where('assistance_type', 'LIKE', '%' . $opt . '%');
                }
                $res[$opt] = $q->count();
            }
        } else {
            foreach ($secOptions as $opt) {
                $q = clone $baseQuery;
                if ($opt === 'Tidak Ada' || $opt === 'Tidak Terdaftar') {
                    $q->where(fn($sub) => $sub->whereNull($secColumn)->orWhere($secColumn, '')->orWhere($secColumn, $opt));
                } else {
                    $q->where($secColumn, '=', $opt);
                }
                $res[$opt] = $q->count();
            }
        }

        return $res;
    }

    private function queryIndicatorCount(
        StatisticCategory $category,
        StatisticIndicator $indicator,
        ?int $selectedDusunId,
    ): int {
        $query = $category->mapping_table === 'families'
            ? Family::query()
            : Citizen::query();

        if ($category->mapping_table === 'citizens') {
            $query->where('status', 'Aktif');
        }

        if ($selectedDusunId) {
            $query->where('dusun_id', $selectedDusunId);
        }

        $this->applyIndicatorCondition($query, $indicator);

        return $query->count();
    }

    private function getGenderBreakdown(
        StatisticCategory $category,
        StatisticIndicator $indicator,
        ?int $selectedDusunId,
        bool $isEmptyDb,
    ): array {
        if ($category->mapping_table !== 'citizens' || $isEmptyDb) {
            return ['male' => 0, 'female' => 0];
        }

        $baseQuery = Citizen::query()
            ->where('status', 'Aktif');

        if ($selectedDusunId) {
            $baseQuery->where('dusun_id', $selectedDusunId);
        }

        $this->applyIndicatorCondition($baseQuery, $indicator);

        return [
            'male' => (clone $baseQuery)->where('gender', 'Laki-laki')->count(),
            'female' => (clone $baseQuery)->where('gender', 'Perempuan')->count(),
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

    private function createStaticDusunCategory(int $currentYear, ?int $selectedDusunId): Collection
    {
        $dusuns = Dusun::orderBy('name', 'asc')->get();
        $indicators = collect();

        foreach ($dusuns as $dusun) {
            if ($selectedDusunId && $dusun->id != $selectedDusunId) {
                continue;
            }

            $male = Citizen::where('status', 'Aktif')->where('dusun_id', $dusun->id)->where('gender', 'Laki-laki')->count();
            $female = Citizen::where('status', 'Aktif')->where('dusun_id', $dusun->id)->where('gender', 'Perempuan')->count();
            $total = $male + $female;

            $liveData = new StatisticData([
                'year' => $currentYear,
                'value' => $total,
                'value_male' => $male,
                'value_female' => $female,
            ]);

            $indicator = new StatisticIndicator([
                'name' => 'Dusun ' . $dusun->name,
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
