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
                    ->required(),
                Select::make('type')->label('Tipe')
                    ->options([
                        'Desa Dalam Angka' => 'Desa Dalam Angka',
                        'Profil Statistik Desa' => 'Profil Statistik Desa',
                        'Infografis' => 'Infografis',
                    ])
                    ->required(),
                TextInput::make('year')->label('Tahun')
                    ->numeric()
                    ->required(),
                FileUpload::make('cover')
                    ->label('Sampul (Cover)')
                    ->image()
                    ->imageResizeTargetWidth(600)
                    ->nullable()
                    ->directory('publications/covers'),
                FileUpload::make('pdf_file')->label('File PDF')
                    ->label('PDF File')
                    ->directory('publications/pdfs')
                    ->acceptedFileTypes(['application/pdf'])
                    ->required(),
            ]);
    }
}
