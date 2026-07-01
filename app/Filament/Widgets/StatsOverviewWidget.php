<?php

namespace App\Filament\Widgets;

use App\Models\Citizen;
use App\Models\Family;
use App\Models\Dusun;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Penduduk', Citizen::where('status', 'Aktif')->count() . ' Jiwa')
                ->description('Warga terdaftar aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Total Keluarga', Family::count() . ' KK')
                ->description('Keluarga terdaftar')
                ->descriptionIcon('heroicon-m-home')
                ->color('info'),
            Stat::make('Wilayah Dusun', Dusun::count() . ' Dusun')
                ->description('Pembagian wilayah dusun')
                ->descriptionIcon('heroicon-m-map')
                ->color('warning'),
        ];
    }
}
