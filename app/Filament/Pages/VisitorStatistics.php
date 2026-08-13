<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TopLocationsTableWidget;
use App\Filament\Widgets\TopPagesTableWidget;
use App\Filament\Widgets\VisitorChartWidget;
use App\Filament\Widgets\VisitorDeviceChartWidget;
use App\Filament\Widgets\VisitorStatsOverviewWidget;
use Filament\Pages\Page;

class VisitorStatistics extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $title = 'Statistik Pengunjung';

    protected static ?string $navigationLabel = 'Pengunjung';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.visitor-statistics';

    protected function getHeaderWidgets(): array
    {
        return [
            VisitorStatsOverviewWidget::class,
            VisitorChartWidget::class,
            VisitorDeviceChartWidget::class,
            TopLocationsTableWidget::class,
            TopPagesTableWidget::class,
        ];
    }
}
