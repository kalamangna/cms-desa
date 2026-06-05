<?php

namespace App\Filament\Resources\StatisticCategories\Pages;

use App\Filament\Resources\StatisticCategories\StatisticCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStatisticCategories extends ListRecords
{
    protected static string $resource = StatisticCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
