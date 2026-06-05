<?php

namespace App\Filament\Resources\Metadata\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class MetadataForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dataset_id')->label('Dataset')
                    ->relationship('dataset', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('source')->label('Sumber'),
                Textarea::make('definition')->label('Definisi')
                    ->columnSpanFull(),
                TextInput::make('update_frequency')->label('Frekuensi Pembaruan'),
                TextInput::make('responsible_person')->label('Penanggung Jawab'),
            ]);
    }
}
