<?php

namespace App\Filament\Resources\Dusuns\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                            ->placeholder('Contoh: Karawa')
                            ->helperText('Tulis nama tanpa kata "Dusun".')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('head_name')
                            ->label('Nama Kepala Dusun')
                            ->placeholder('Contoh: Andi Amran')
                            ->helperText('Nama pejabat Kepala Dusun.'),
                        TextInput::make('total_rt')
                            ->label('Jumlah RT')
                            ->placeholder('Contoh: 4')
                            ->helperText('Jumlah RT di dusun ini.')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        TextInput::make('total_rw')
                            ->label('Jumlah RW')
                            ->placeholder('Contoh: 2')
                            ->helperText('Jumlah RW di dusun ini.')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Peta Spasial (Batas Wilayah)')
                    ->description('Masukkan koordinat GeoJSON (warna area akan diacak otomatis).')
                    ->schema([
                        Textarea::make('geojson')
                            ->label('Data GeoJSON Poligon')
                            ->placeholder('{"type": "Feature", "geometry": {"type": "Polygon", "coordinates": [...]}}')
                            ->helperText('Tempel data GeoJSON polygon batas dusun.')
                            ->rows(6)
                            ->nullable(),
                    ])
                    ->columns(1),
            ])
            ->columns(1);
    }
}
