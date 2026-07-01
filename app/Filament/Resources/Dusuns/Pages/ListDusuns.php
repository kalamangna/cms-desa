<?php

namespace App\Filament\Resources\Dusuns\Pages;

use App\Filament\Resources\Dusuns\DusunResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDusuns extends ListRecords
{
    protected static string $resource = DusunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
