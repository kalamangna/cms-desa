<?php

namespace App\Filament\Resources\PopupInfographics\Pages;

use App\Filament\Resources\PopupInfographics\PopupInfographicResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPopupInfographic extends EditRecord
{
    protected static string $resource = PopupInfographicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
