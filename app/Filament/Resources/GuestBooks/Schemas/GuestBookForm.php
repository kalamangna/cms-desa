<?php

namespace App\Filament\Resources\GuestBooks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class GuestBookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama Lengkap')
                    ->required(),
                TextInput::make('institution_address')->label('Instansi / Alamat')
                    ->required(),
                TextInput::make('phone')->label('Nomor Kontak (WhatsApp)')
                    ->required(),
                Textarea::make('purpose')->label('Keperluan / Pesan')
                    ->rows(4)
                    ->required(),
            ]);
    }
}
