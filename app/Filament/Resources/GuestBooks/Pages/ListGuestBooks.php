<?php

namespace App\Filament\Resources\GuestBooks\Pages;

use App\Filament\Resources\GuestBooks\GuestBookResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGuestBooks extends ListRecords
{
    protected static string $resource = GuestBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
