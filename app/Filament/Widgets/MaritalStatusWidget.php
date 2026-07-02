<?php

namespace App\Filament\Widgets;

use App\Models\Citizen;
use Filament\Widgets\ChartWidget;

class MaritalStatusWidget extends ChartWidget
{
    protected ?string $heading = 'Status Perkawinan';

    protected static ?int $sort = 4;

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $belumKawin = Citizen::where('marital_status', 'Belum Kawin')->count();
        $kawin = Citizen::where('marital_status', 'Kawin')->count();
        $ceraiHidup = Citizen::where('marital_status', 'Cerai Hidup')->count();
        $ceraiMati = Citizen::where('marital_status', 'Cerai Mati')->count();

        // Fallback jika database kosong
        if ($belumKawin === 0 && $kawin === 0 && $ceraiHidup === 0 && $ceraiMati === 0) {
            $belumKawin = 10;
            $kawin = 18;
            $ceraiHidup = 2;
            $ceraiMati = 3;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Jiwa',
                    'data' => [$belumKawin, $kawin, $ceraiHidup, $ceraiMati],
                    'backgroundColor' => [
                        '#6366f1', // Indigo (Belum Kawin)
                        '#10b981', // Emerald (Kawin)
                        '#f59e0b', // Amber (Cerai Hidup)
                        '#ef4444', // Red (Cerai Mati)
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
