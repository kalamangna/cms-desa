<?php

namespace App\Filament\Resources\PopupInfographics\Pages;

use App\Filament\Resources\PopupInfographics\PopupInfographicResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPopupInfographics extends ListRecords
{
    protected static string $resource = PopupInfographicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
