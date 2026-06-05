<?php

namespace App\Filament\Resources\StatisticData\Pages;

use App\Filament\Resources\StatisticData\StatisticDataResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStatisticData extends ListRecords
{
    protected static string $resource = StatisticDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
