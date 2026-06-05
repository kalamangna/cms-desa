<?php

namespace App\Filament\Resources\Metadata\Pages;

use App\Filament\Resources\Metadata\MetadataResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMetadata extends ListRecords
{
    protected static string $resource = MetadataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
