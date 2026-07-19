<?php

namespace App\Filament\Resources\PublicFacilities\Pages;

use App\Filament\Resources\PublicFacilities\PublicFacilityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPublicFacility extends EditRecord
{
    protected static string $resource = PublicFacilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
