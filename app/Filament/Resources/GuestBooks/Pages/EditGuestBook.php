<?php

namespace App\Filament\Resources\GuestBooks\Pages;

use App\Filament\Resources\GuestBooks\GuestBookResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGuestBook extends EditRecord
{
    protected static string $resource = GuestBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
