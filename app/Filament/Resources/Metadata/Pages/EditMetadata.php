<?php

namespace App\Filament\Resources\Metadata\Pages;

use App\Filament\Resources\Metadata\MetadataResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditMetadata extends EditRecord
{
    protected static string $resource = MetadataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
