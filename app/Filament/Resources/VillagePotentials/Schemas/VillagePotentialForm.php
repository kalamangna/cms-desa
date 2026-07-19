<?php

namespace App\Filament\Resources\VillagePotentials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VillagePotentialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('Judul Potensi')
                    ->required()
                    ->maxLength(255),
                Select::make('category')->label('Kategori')
                    ->options([
                        'Pariwisata' => 'Pariwisata',
                        'Pertanian & Perkebunan' => 'Pertanian & Perkebunan',
                        'Peternakan' => 'Peternakan',
                        'Industri Kreatif' => 'Industri Kreatif',
                        'Seni & Budaya' => 'Seni & Budaya',
                    ])
                    ->required(),
                RichEditor::make('description')->label('Deskripsi')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('image')->label('Foto Pendukung')
                    ->image()
                    ->imageResizeTargetWidth(1000)
                    ->nullable()
                    ->directory('potentials'),
                Toggle::make('is_active')->label('Status Aktif')
                    ->default(true),
            ]);
    }
}
