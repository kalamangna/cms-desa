<?php

namespace App\Http\Controllers;

use App\Models\StatisticCategory;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index()
    {
        $categories = StatisticCategory::with(['indicators.data' => function($query) {
            $query->orderBy('year', 'asc');
        }])->get();

        $citizenCount = \App\Models\Citizen::where('status', 'Aktif')->count();
        if ($citizenCount > 0) {
            $currentYear = date('Y');
            foreach ($categories as $category) {
                foreach ($category->indicators as $indicator) {
                    $value = 0;
                    if ($category->name === 'Penduduk') {
                        if (strpos(strtolower($indicator->name), 'laki-laki') !== false) {
                            $value = \App\Models\Citizen::where('status', 'Aktif')->where('gender', 'Laki-laki')->count();
                        } elseif (strpos(strtolower($indicator->name), 'perempuan') !== false) {
                            $value = \App\Models\Citizen::where('status', 'Aktif')->where('gender', 'Perempuan')->count();
                        }
                    } elseif ($category->name === 'Pendidikan') {
                        $value = \App\Models\Citizen::where('status', 'Aktif')
                            ->where(function($q) use ($indicator) {
                                $q->where('education_level', 'LIKE', '%' . $indicator->name . '%')
                                  ->orWhere('education', 'LIKE', '%' . $indicator->name . '%');
                            })->count();
                    } elseif ($category->name === 'Pekerjaan') {
                        if ($indicator->name === 'Lainnya') {
                            $value = \App\Models\Citizen::where('status', 'Aktif')
                                ->where(function($q) {
                                    $q->whereNotIn('job', ['Nelayan', 'Petani', 'PNS', 'Wiraswasta'])
                                      ->orWhereNull('job');
                                })
                                ->count();
                        } else {
                            $value = \App\Models\Citizen::where('status', 'Aktif')
                                ->where('job', 'LIKE', '%' . $indicator->name . '%')
                                ->count();
                        }
                    } elseif ($category->name === 'Kemiskinan') {
                        $value = \App\Models\Family::whereNotNull('assistance_type')
                            ->where('assistance_type', '!=', '')
                            ->where('assistance_type', '!=', 'Tidak Ada')
                            ->where('assistance_type', '!=', 'Tidak')
                            ->count();
                    } else {
                        // Fallback to existing data point if no custom rule
                        continue;
                    }

                    $newData = new \App\Models\StatisticData([
                        'year' => $currentYear,
                        'value' => $value,
                    ]);
                    $indicator->setRelation('data', collect([$newData]));
                }
            }
        }

        return view('statistics.index', compact('categories'));
    }
}
