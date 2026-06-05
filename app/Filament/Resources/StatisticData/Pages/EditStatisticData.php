<?php

namespace App\Filament\Resources\StatisticData\Pages;

use App\Filament\Resources\StatisticData\StatisticDataResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStatisticData extends EditRecord
{
    protected static string $resource = StatisticDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
