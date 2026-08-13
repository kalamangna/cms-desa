<?php

namespace App\Filament\Widgets;

use App\Models\VisitorLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class VisitorDeviceChartWidget extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Distribusi Perangkat Pengunjung';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $logs = VisitorLog::query()
            ->select('user_agent', DB::raw('count(distinct ip_hash) as total'))
            ->groupBy('user_agent')
            ->get();

        $mobile = 0;
        $tablet = 0;
        $desktop = 0;

        foreach ($logs as $log) {
            $ua = strtolower($log->user_agent ?? '');
            $count = (int) $log->total;

            if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet') || str_contains($ua, 'playbook') || str_contains($ua, 'silk')) {
                $tablet += $count;
            } elseif (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone') || str_contains($ua, 'ipod') || str_contains($ua, 'blackberry') || str_contains($ua, 'windows phone')) {
                $mobile += $count;
            } else {
                $desktop += $count;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pengunjung Unik',
                    'data' => [$mobile, $desktop, $tablet],
                    'backgroundColor' => [
                        '#10b981', // Mobile - Green
                        '#3b82f6', // Desktop - Blue
                        '#f59e0b', // Tablet - Amber
                    ],
                ],
            ],
            'labels' => ['Mobile (HP)', 'Desktop (Komputer)', 'Tablet'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
