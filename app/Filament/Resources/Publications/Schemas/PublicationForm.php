<?php

namespace App\Filament\Resources\Publications\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class PublicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('type')
                    ->options([
                        'Desa Dalam Angka' => 'Desa Dalam Angka',
                        'Profil Statistik Desa' => 'Profil Statistik Desa',
                        'Infografis' => 'Infografis',
                    ])
                    ->required(),
                TextInput::make('year')
                    ->numeric()
                    ->required(),
                FileUpload::make('cover')
                    ->image()
                    ->directory('publications/covers'),
                FileUpload::make('pdf_file')
                    ->label('PDF File')
                    ->directory('publications/pdfs')
                    ->acceptedFileTypes(['application/pdf'])
                    ->required(),
            ]);
    }
}
