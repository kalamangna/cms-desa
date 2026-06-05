<?php

namespace App\Filament\Resources\Officials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;

class OfficialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama')
                    ->required(),
                TextInput::make('position')->label('Jabatan')
                    ->required(),
                FileUpload::make('photo')->label('Foto')
                    ->image()
                    ->directory('officials'),
            ]);
    }
}
