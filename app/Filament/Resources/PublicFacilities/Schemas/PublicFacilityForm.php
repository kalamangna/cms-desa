<?php

namespace App\Filament\Resources\PublicFacilities\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class PublicFacilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('name')->label('Nama Fasilitas')
                            ->placeholder('Contoh: Puskesmas Pembantu Dusun Karawa')
                            ->helperText('Nama resmi fasilitas umum desa.')
                            ->required(),
                        Select::make('type')->label('Kategori Fasilitas')
                            ->placeholder('Pilih Kategori')
                            ->helperText('Jenis fasilitas sarana & prasarana.')
                            ->options([
                                'Pendidikan' => 'Pendidikan',
                                'Ibadah' => 'Ibadah',
                                'Kesehatan' => 'Kesehatan',
                                'Pemerintahan' => 'Pemerintahan',
                                'Umum' => 'Lainnya / Umum',
                            ])
                            ->required(),
                    ])
                    ->columnSpanFull(),
                Grid::make(2)
                    ->schema([
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                if (empty($state)) {
                                    return;
                                }
                                if (str_contains($state, ',')) {
                                    $parts = explode(',', $state);
                                    $lat = trim($parts[0]);
                                    $lng = trim($parts[1]);
                                    if (is_numeric($lat) && is_numeric($lng)) {
                                        $set('latitude', $lat);
                                        $set('longitude', $lng);
                                    }
                                } elseif (str_contains($state, 'google.com/maps') || str_contains($state, 'goo.gl/maps')) {
                                    if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $state, $matches)) {
                                        $set('latitude', $matches[1]);
                                        $set('longitude', $matches[2]);
                                    }
                                }
                            })
                            ->minValue(-90.0)
                            ->maxValue(90.0)
                            ->required()
                            ->rules(['numeric', 'regex:/^-?\d+(\.\d+)?$/'])
                            ->validationMessages([
                                'numeric' => 'Latitude harus berupa angka.',
                                'regex' => 'Format Latitude harus menggunakan tanda titik (.) desimal (contoh: -5.1010335).',
                                'min_value' => 'Latitude tidak boleh kurang dari -90.',
                                'max_value' => 'Latitude tidak boleh lebih dari 90.',
                            ])
                            ->helperText('Contoh: -5.1010335. Bisa tempel link Maps.'),
                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                if (empty($state)) {
                                    return;
                                }
                                if (str_contains($state, ',')) {
                                    $parts = explode(',', $state);
                                    $lat = trim($parts[0]);
                                    $lng = trim($parts[1]);
                                    if (is_numeric($lat) && is_numeric($lng)) {
                                        $set('latitude', $lat);
                                        $set('longitude', $lng);
                                    }
                                }
                            })
                            ->minValue(-180.0)
                            ->maxValue(180.0)
                            ->required()
                            ->rules(['numeric', 'regex:/^-?\d+(\.\d+)?$/'])
                            ->validationMessages([
                                'numeric' => 'Longitude harus berupa angka.',
                                'regex' => 'Format Longitude harus menggunakan tanda titik (.) desimal (contoh: 120.0967011).',
                                'min_value' => 'Longitude tidak boleh kurang dari -180.',
                                'max_value' => 'Longitude tidak boleh lebih dari 180.',
                            ])
                            ->helperText('Contoh: 120.0967011'),
                    ])
                    ->columnSpanFull(),
                TextInput::make('address')->label('Alamat')
                    ->placeholder('Contoh: Dusun Karawa, RT 003/RW 001')
                    ->helperText('Alamat lengkap lokasi fasilitas.')
                    ->columnSpanFull(),
                Textarea::make('description')->label('Deskripsi / Catatan')
                    ->placeholder('Contoh: Gedung dua lantai, beroperasi setiap hari kerja...')
                    ->helperText('Catatan atau keterangan tambahan fasilitas.')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
