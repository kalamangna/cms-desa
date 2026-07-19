<?php

namespace App\Filament\Resources\GuestBooks\Pages;

use App\Filament\Resources\GuestBooks\GuestBookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGuestBook extends CreateRecord
{
    protected static string $resource = GuestBookResource::class;
}
