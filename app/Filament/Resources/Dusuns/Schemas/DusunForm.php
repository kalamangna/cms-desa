<?php

namespace App\Filament\Resources\Dusuns\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\TextInput;

class DusunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama Dusun')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('head_name')->label('Nama Kepala Dusun'),
            ]);
    }
}
