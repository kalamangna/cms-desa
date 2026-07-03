<?php

namespace App\Http\Controllers;

use App\Models\StatisticCategory;
use App\Models\Dataset;
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
                                    $q->whereNotIn('job', ['Petani', 'Wiraswasta', 'PNS', 'Buruh', 'Tidak Bekerja'])
                                      ->orWhereNull('job');
                                })
                                ->count();
                        } else {
                            $value = \App\Models\Citizen::where('status', 'Aktif')
                                ->where('job', 'LIKE', '%' . $indicator->name . '%')
                                ->count();
                        }
                    } elseif ($category->name === 'Disabilitas') {
                        $col = null;
                        if ($indicator->name === 'Disabilitas Fisik') $col = 'disability_physical';
                        elseif ($indicator->name === 'Disabilitas Mental') $col = 'disability_mental';
                        elseif ($indicator->name === 'Disabilitas Intelektual') $col = 'disability_intellectual';
                        elseif ($indicator->name === 'Disabilitas Sensorik Netra') $col = 'disability_blind';
                        elseif ($indicator->name === 'Disabilitas Sensorik Rungu') $col = 'disability_deaf';
                        elseif ($indicator->name === 'Disabilitas Sensorik Wicara') $col = 'disability_speech';

                        if ($col) {
                            $value = \App\Models\Citizen::where('status', 'Aktif')->where($col, 'Ya')->count();
                        }
                    } elseif ($category->name === 'Penyakit Kronis') {
                        $columnMap = [
                            'Hipertensi' => 'illness_hypertension',
                            'Rematik' => 'illness_rheumatic',
                            'Asma' => 'illness_asthma',
                            'Masalah Jantung' => 'illness_heart',
                            'Diabetes' => 'illness_diabetes',
                            'TBC' => 'illness_tbc',
                            'Stroke' => 'illness_stroke',
                            'Kanker' => 'illness_cancer',
                            'Gagal Ginjal' => 'illness_kidney',
                            'Kolesterol' => 'illness_cholesterol',
                            'Lainnya' => 'illness_other',
                        ];
                        $col = $columnMap[$indicator->name] ?? null;
                        if ($col) {
                            $value = \App\Models\Citizen::where('status', 'Aktif')->where($col, 'Ya')->count();
                        }
                    } elseif ($category->name === 'Bantuan Sosial') {
                        if ($indicator->name === 'Penerima Bantuan') {
                            $value = \App\Models\Family::whereNotNull('assistance_type')
                                ->where('assistance_type', '!=', '')
                                ->where('assistance_type', '!=', 'Tidak Ada')
                                ->where('assistance_type', '!=', 'Tidak')
                                ->count();
                        } else {
                            $value = \App\Models\Family::where(function($q) {
                                $q->whereNull('assistance_type')
                                  ->orWhere('assistance_type', '')
                                  ->orWhere('assistance_type', 'Tidak Ada')
                                  ->orWhere('assistance_type', 'Tidak');
                            })->count();
                        }
                    } elseif ($category->name === 'Kepemilikan Rumah') {
                        if ($indicator->name === 'Milik Sendiri') {
                            $value = \App\Models\Family::where('ownership_status', 'Milik sendiri')->count();
                        } elseif ($indicator->name === 'Sewa/Kontrak') {
                            $value = \App\Models\Family::where(function($q) {
                                $q->where('ownership_status', 'LIKE', '%Sewa%')
                                  ->orWhere('ownership_status', 'LIKE', '%Kontrak%');
                            })->count();
                        } else {
                            $value = \App\Models\Family::whereNotIn('ownership_status', ['Milik sendiri'])
                                ->where('ownership_status', 'NOT LIKE', '%Sewa%')
                                ->where('ownership_status', 'NOT LIKE', '%Kontrak%')
                                ->count();
                        }
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

        $datasets = Dataset::latest()->take(3)->get();

        return view('statistics.index', compact('categories', 'datasets'));
    }
}
