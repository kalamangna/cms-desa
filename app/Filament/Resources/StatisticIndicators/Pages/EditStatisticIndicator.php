<?php

namespace App\Filament\Resources\StatisticIndicators\Pages;

use App\Filament\Resources\StatisticIndicators\StatisticIndicatorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStatisticIndicator extends EditRecord
{
    protected static string $resource = StatisticIndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
