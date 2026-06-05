<?php

namespace App\Filament\Resources\StatisticIndicators\Pages;

use App\Filament\Resources\StatisticIndicators\StatisticIndicatorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStatisticIndicators extends ListRecords
{
    protected static string $resource = StatisticIndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
