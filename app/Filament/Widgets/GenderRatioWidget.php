<?php

namespace App\Filament\Widgets;

use App\Models\Citizen;
use Filament\Widgets\ChartWidget;

class GenderRatioWidget extends ChartWidget
{
    protected ?string $heading = 'Rasio Jenis Kelamin';

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $maleCount = Citizen::where('gender', 'Laki-laki')->count();
        $femaleCount = Citizen::where('gender', 'Perempuan')->count();

        // Fallback jika database masih kosong agar grafik tidak kosong melompong
        if ($maleCount === 0 && $femaleCount === 0) {
            $maleCount = 12;
            $femaleCount = 15;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Jiwa',
                    'data' => [$maleCount, $femaleCount],
                    'backgroundColor' => [
                        '#0ea5e9', // Sky Blue (Laki-laki)
                        '#ec4899', // Pink/Rose (Perempuan)
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['Laki-laki', 'Perempuan'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
