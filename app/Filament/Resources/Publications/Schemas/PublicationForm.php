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
                TextInput::make('title')->label('Judul')
                    ->required()
                    ->columnSpanFull(),
                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        Select::make('type')->label('Tipe')
                            ->options([
                                'Desa Dalam Angka' => 'Desa Dalam Angka',
                                'Profil Statistik Desa' => 'Profil Statistik Desa',
                                'Infografis' => 'Infografis',
                            ])
                            ->required(),
                        TextInput::make('year')->label('Tahun')
                            ->numeric()
                            ->default(date('Y'))
                            ->required(),
                    ])
                    ->columnSpanFull(),
                FileUpload::make('cover')
                    ->label('Sampul (Cover)')
                    ->image()
                    ->imageResizeTargetWidth(600)
                    ->nullable()
                    ->directory('publications/covers')
                    ->columnSpanFull(),
                FileUpload::make('pdf_file')->label('File PDF')
                    ->directory('publications/pdfs')
                    ->acceptedFileTypes(['application/pdf'])
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
