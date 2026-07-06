<?php

namespace App\Http\Controllers;

use App\Models\StatisticCategory;
use App\Models\Dataset;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index()
    {
        // Ambil semua kategori aktif beserta indikator dan data historis dari DB
        $categories = StatisticCategory::where('is_active', true)
            ->with(['indicators.data' => function ($query) {
                $query->orderBy('year', 'asc');
            }])->get();

        $currentYear = date('Y');
        
        $totalCitizens = \App\Models\Citizen::count();
        $totalFamilies = \App\Models\Family::count();
        $isEmptyDb = ($totalCitizens === 0 && $totalFamilies === 0);

        foreach ($categories as $category) {
            // Guard: lewati kategori yang mapping_table-nya tidak valid
            if (! $category->mapping_table) {
                continue;
            }
            if (! in_array($category->mapping_table, StatisticCategory::ALLOWED_TABLES, true)) {
                continue;
            }
            $allowedColumns = StatisticCategory::ALLOWED_COLUMNS[$category->mapping_table] ?? [];

            foreach ($category->indicators as $indicator) {
                // Guard: lewati indikator dengan mapping_column tidak dalam whitelist
                if (! $indicator->mapping_column
                    || ! in_array($indicator->mapping_column, $allowedColumns, true)) {
                    continue;
                }

                // Hitung nilai real-time dari DB untuk tahun ini
                $query = $category->mapping_table === 'families'
                    ? \App\Models\Family::query()
                    : \App\Models\Citizen::query();

                if ($category->mapping_table === 'citizens') {
                    $query->where('status', 'Aktif');
                }

                $operator = $indicator->mapping_operator ?: '=';
                $val      = $indicator->mapping_value;

                if ($operator === 'LIKE') {
                    $query->where($indicator->mapping_column, 'LIKE', '%' . $val . '%');
                } elseif ($operator === 'whereNotNull') {
                    $query->whereNotNull($indicator->mapping_column)
                          ->where($indicator->mapping_column, '!=', '')
                          ->where($indicator->mapping_column, '!=', 'Tidak Ada')
                          ->where($indicator->mapping_column, '!=', 'Tidak');
                } elseif ($operator === 'whereNullOrTidak') {
                    $query->where(function ($q) use ($indicator) {
                        $q->whereNull($indicator->mapping_column)
                          ->orWhere($indicator->mapping_column, '')
                          ->orWhere($indicator->mapping_column, 'Tidak Ada')
                          ->orWhere($indicator->mapping_column, 'Tidak');
                    });
                } elseif ($operator === 'whereNotIn') {
                    $arr = array_map('trim', explode(',', (string) $val));
                    $query->where(function ($q) use ($indicator, $arr) {
                        $q->whereNotIn($indicator->mapping_column, $arr)
                          ->orWhereNull($indicator->mapping_column);
                    });
                } else {
                    // Operator '=' — jika mapping_value null, gunakan whereNull
                    if (is_null($val)) {
                        $query->whereNull($indicator->mapping_column);
                    } else {
                        $query->where($indicator->mapping_column, $operator, $val);
                    }
                }

                $liveValue = $isEmptyDb ? 0 : $query->count();

                // Buat objek StatisticData sementara untuk data real-time tahun ini
                $liveData = new \App\Models\StatisticData([
                    'year'  => $currentYear,
                    'value' => $liveValue,
                ]);

                // Pertahankan data historis dari DB; replace/tambahkan entry tahun ini saja
                $historicalData = $indicator->data->filter(
                    fn ($d) => (int) $d->year !== (int) $currentYear
                );

                if ($isEmptyDb) {
                    $historicalData = $historicalData->map(function ($d) {
                        $d->value = 0;
                        return $d;
                    });
                }

                $mergedData = $historicalData->push($liveData)->sortBy('year')->values();

                $indicator->setRelation('data', $mergedData);
            }
        }

        $datasets = Dataset::latest()->take(3)->get();

        return view('statistics.index', compact('categories', 'datasets'));
    }
}
