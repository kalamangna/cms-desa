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
                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        TextInput::make('title')->label('Judul Potensi')
                            ->placeholder('Contoh: Wisata Alam Air Terjun Karawa')
                            ->helperText('Nama potensi keunggulan desa.')
                            ->required()
                            ->maxLength(255),
                        Select::make('category')->label('Kategori')
                            ->placeholder('Pilih Kategori')
                            ->helperText('Kelompok sektor potensi desa.')
                            ->options([
                                'Pariwisata' => 'Pariwisata',
                                'Pertanian & Perkebunan' => 'Pertanian & Perkebunan',
                                'Peternakan' => 'Peternakan',
                                'Industri Kreatif' => 'Industri Kreatif',
                                'Seni & Budaya' => 'Seni & Budaya',
                            ])
                            ->required(),
                    ])
                    ->columnSpanFull(),
                RichEditor::make('description')->label('Deskripsi')
                    ->placeholder('Uraikan keunggulan dan daya tarik potensi desa ini...')
                    ->helperText('Penjelasan rinci mengenai potensi desa.')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('image')->label('Foto Pendukung')
                    ->helperText('Unggah foto potensi desa (Maksimal 2MB).')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->imageResizeTargetWidth(1000)
                    ->maxSize(2048)
                    ->nullable()
                    ->directory('potentials')
                    ->columnSpanFull(),
            ]);
    }
}
