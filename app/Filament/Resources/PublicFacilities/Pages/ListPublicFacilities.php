<?php

namespace App\Filament\Resources\PublicFacilities\Pages;

use App\Filament\Resources\PublicFacilities\PublicFacilityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPublicFacilities extends ListRecords
{
    protected static string $resource = PublicFacilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
