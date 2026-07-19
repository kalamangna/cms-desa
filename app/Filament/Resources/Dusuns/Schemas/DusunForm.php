<?php

namespace App\Filament\Resources\Dusuns\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;

class DusunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dusun')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Dusun')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('head_name')
                            ->label('Nama Kepala Dusun'),
                        TextInput::make('total_rt')
                            ->label('Jumlah RT')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        TextInput::make('total_rw')
                            ->label('Jumlah RW')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Peta Spasial (Batas Wilayah)')
                    ->description('Masukkan data koordinat batas wilayah dusun dalam format GeoJSON. Sistem akan mengacak warna tampilan area masing-masing dusun secara otomatis di halaman publik.')
                    ->schema([
                        Textarea::make('geojson')
                            ->label('Data GeoJSON Poligon')
                            ->placeholder('{"type": "Feature", "geometry": {"type": "Polygon", "coordinates": [...]}}')
                            ->rows(6)
                            ->nullable(),
                    ])
                    ->columns(1),
            ])
            ->columns(1);
    }
}
