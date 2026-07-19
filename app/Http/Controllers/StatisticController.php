<?php

namespace App\Http\Controllers;

use App\Models\Dusun;
use App\Services\StatisticService;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function __construct(
        private StatisticService $statisticService,
    ) {}

    public function index(Request $request)
    {
        $selectedDusunId = $this->validateDusunId($request->query('dusun_id'));
        $selectedYear = $this->validateYear($request->query('year'));
        $selectedKategori = $request->query('kategori');

        $result = $this->statisticService->getStatisticsData($selectedDusunId, $selectedYear);

        if ($request->ajax() || $request->query('json') == 1) {
            return response()->json(
                $this->statisticService->formatJsonResponse($result['categories'])
            );
        }

        return view('statistics.index', [
            'categories' => $result['categories'],
            'datasets' => $result['datasets'],
            'isEmptyDb' => $result['isEmptyDb'],
            'dusuns' => $result['dusuns'],
            'availableYears' => $result['availableYears'],
            'selectedDusunId' => $selectedDusunId,
            'selectedYear' => $selectedYear ?? (string) date('Y'),
            'selectedKategori' => $selectedKategori,
            'summaryCards' => $result['summaryCards'],
        ]);
    }

    private function validateDusunId($value): ?int
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        $id = (int) $value;

        if (! Dusun::where('id', $id)->exists()) {
            return null;
        }

        return $id;
    }

    private function validateYear($value): ?int
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        $year = (int) $value;
        $currentYear = (int) date('Y');

        if ($year < 2000 || $year > $currentYear + 1) {
            return $currentYear;
        }

        return $year;
    }
}
