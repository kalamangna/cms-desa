<?php

namespace App\Filament\Resources\VillagePotentials\Pages;

use App\Filament\Resources\VillagePotentials\VillagePotentialResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVillagePotential extends EditRecord
{
    protected static string $resource = VillagePotentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
