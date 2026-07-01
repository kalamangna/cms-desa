<?php

namespace App\Filament\Resources\Dusuns\Pages;

use App\Filament\Resources\Dusuns\DusunResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDusun extends EditRecord
{
    protected static string $resource = DusunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
