<?php

namespace App\Filament\Resources\PublicFacilities\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class PublicFacilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama Fasilitas')
                    ->required(),
                Select::make('type')->label('Kategori Fasilitas')
                    ->options([
                        'Pendidikan' => 'Pendidikan',
                        'Ibadah' => 'Ibadah',
                        'Kesehatan' => 'Kesehatan',
                        'Pemerintahan' => 'Pemerintahan',
                        'Umum' => 'Lainnya / Umum',
                    ])
                    ->required(),
                TextInput::make('latitude')
                    ->label('Latitude')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        if (empty($state)) return;
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
                    ->helperText('Contoh: -5.1010335. Bisa juga langsung tempel koordinat dari Google Maps (misal: -5.23, 120.21) di sini.'),
                TextInput::make('longitude')
                    ->label('Longitude')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        if (empty($state)) return;
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
                TextInput::make('address')->label('Alamat'),
                Textarea::make('description')->label('Deskripsi / Catatan')
                    ->rows(3),
            ]);
    }
}
