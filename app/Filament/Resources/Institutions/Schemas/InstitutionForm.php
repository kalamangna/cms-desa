<?php

namespace App\Filament\Resources\Institutions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class InstitutionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lembaga')
                    ->required(),
                FileUpload::make('logo')
                    ->label('Logo Lembaga')
                    ->image()
                    ->nullable()
                    ->directory('institutions')
                    ->columnSpanFull(),
            ]);
    }
}
