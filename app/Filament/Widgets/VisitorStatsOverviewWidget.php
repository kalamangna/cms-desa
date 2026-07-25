<?php

namespace App\Filament\Widgets;

use App\Models\VisitorLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class VisitorStatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $todayStr = now()->toDateString();
        $yesterdayStr = now()->subDay()->toDateString();

        $today = VisitorLog::where('visit_date', $todayStr)->distinct('ip_hash')->count('ip_hash');
        $yesterday = VisitorLog::where('visit_date', $yesterdayStr)->distinct('ip_hash')->count('ip_hash');
        
        $thisMonth = VisitorLog::whereYear('visit_date', now()->year)
            ->whereMonth('visit_date', now()->month)
            ->distinct('ip_hash')
            ->count('ip_hash');

        $totalUnique = VisitorLog::distinct('ip_hash')->count('ip_hash');

        // Persentase pertumbuhan dibanding kemarin
        $diff = $today - $yesterday;
        $description = $diff >= 0 ? "+{$diff} dibanding kemarin" : "{$diff} dibanding kemarin";
        $descriptionIcon = $diff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        $color = $diff >= 0 ? 'success' : 'danger';

        return [
            Stat::make('Pengunjung Hari Ini', number_format($today, 0, ',', '.'))
                ->description($description)
                ->descriptionIcon($descriptionIcon)
                ->color($color),

            Stat::make('Pengunjung Kemarin', number_format($yesterday, 0, ',', '.')),

            Stat::make('Pengunjung Bulan Ini', number_format($thisMonth, 0, ',', '.'))
                ->description(now()->translatedFormat('F Y'))
                ->color('info'),

            Stat::make('Total Pengunjung Unik', number_format($totalUnique, 0, ',', '.'))
                ->description('Keseluruhan')
                ->color('warning'),
        ];
    }
}
