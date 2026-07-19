<?php

namespace App\Filament\Resources\VillagePotentials\Pages;

use App\Filament\Resources\VillagePotentials\VillagePotentialResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVillagePotentials extends ListRecords
{
    protected static string $resource = VillagePotentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
