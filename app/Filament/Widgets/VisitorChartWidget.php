<?php

namespace App\Filament\Widgets;

use App\Models\VisitorLog;
use Filament\Widgets\ChartWidget;

class VisitorChartWidget extends ChartWidget
{
    protected ?string $heading = 'Statistik Pengunjung Website';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getFilters(): ?array
    {
        return [
            '7' => '7 Hari Terakhir',
            '15' => '15 Hari Terakhir',
            '30' => '30 Hari Terakhir',
        ];
    }

    protected function getData(): array
    {
        $days = (int) ($this->filter ?? '7');
        
        $data = collect(range($days - 1, 0))
            ->map(function ($i) {
                $date = now()->subDays($i)->format('Y-m-d');
                
                $pageViews = VisitorLog::where('visit_date', $date)->count();
                $uniqueVisitors = VisitorLog::where('visit_date', $date)
                    ->distinct('ip_hash')
                    ->count('ip_hash');
                    
                return [
                    'date' => now()->subDays($i)->translatedFormat('d M'),
                    'views' => $pageViews,
                    'visitors' => $uniqueVisitors,
                ];
            });

        return [
            'datasets' => [
                [
                    'label' => 'Total Kunjungan (Page Views)',
                    'data' => $data->pluck('views')->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.05)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pengunjung Unik',
                    'data' => $data->pluck('visitors')->toArray(),
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.05)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
