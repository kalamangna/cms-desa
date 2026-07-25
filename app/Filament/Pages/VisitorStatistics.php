<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\VisitorStatsOverviewWidget;
use App\Filament\Widgets\VisitorChartWidget;
use App\Filament\Widgets\TopPagesTableWidget;

class VisitorStatistics extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Statistik Pengunjung Website';
    protected static ?string $navigationLabel = 'Statistik Pengunjung';
    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.visitor-statistics';

    protected function getHeaderWidgets(): array
    {
        return [
            VisitorStatsOverviewWidget::class,
            VisitorChartWidget::class,
            TopPagesTableWidget::class,
        ];
    }
}
