<?php

namespace App\Filament\Resources\StatisticCategories\Pages;

use App\Filament\Resources\StatisticCategories\StatisticCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStatisticCategory extends EditRecord
{
    protected static string $resource = StatisticCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
