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
            'dusuns' => Dusun::orderBy('name', 'asc')->get(),
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
            foreach ($category->indicators as $indicator) {
                $indicatorsData[] = [
                    'name' => $indicator->name,
                    'unit' => $indicator->unit,
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
                'years' => $category->indicators
                    ->flatMap(fn ($i) => $i->data->pluck('year'))
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
            $query = Dusun::withCount(['citizens' => fn ($q) => $q->where('status', 'Aktif')]);

            if ($selectedDusunId) {
                $query->where('id', $selectedDusunId);
            }

            $topDusun = $query->orderByDesc('citizens_count')->first();
        }

        return [
            'total_penduduk' => $totalPendudukNow,
            'yoy_growth' => $yoyGrowth,
            'latest_year' => $currentYear,
            'top_dusun' => $topDusun?->name ?? '-',
            'top_dusun_count' => $topDusun?->citizens_count ?? 0,
        ];
    }

    /**
     * Hitung pertumbuhan year-on-year berdasarkan data yang setara:
     * jumlah penduduk aktif tahun ini vs jumlah penduduk aktif tahun lalu.
     */
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

        foreach ($categories as $category) {
            if (! $category->mapping_table) {
                continue;
            }
            if (! in_array($category->mapping_table, StatisticCategory::ALLOWED_TABLES, true)) {
                continue;
            }

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

    /**
     * Terapkan kondisi query berdasarkan indikator mapping.
     */
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
        $historicalData = $indicator->data->filter(
            fn ($d) => (int) $d->year !== $currentYear,
        );

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
}
