<?php

namespace App\Filament\Resources\Galleries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class GalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        TextInput::make('title')->label('Judul')
                            ->placeholder('Contoh: Pelaksanaan Kerja Bakti Dusun')
                            ->helperText('Judul atau nama kegiatan galeri.')
                            ->required(),
                        Select::make('type')->label('Tipe Galeri')
                            ->placeholder('Pilih Tipe Galeri')
                            ->helperText('Tipe media galeri kegiatan.')
                            ->options([
                                'foto' => 'Foto',
                                'video' => 'Video',
                            ])
                            ->default('foto')
                            ->required()
                            ->live(),
                    ])
                    ->columnSpanFull(),
                FileUpload::make('image')->label('Foto')
                    ->helperText('Unggah foto dokumentasi kegiatan (Otomatis dioptimalkan WebP, maks 2MB).')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->imageResizeTargetWidth(1200)
                    ->maxSize(2048)
                    ->directory('galleries')
                    ->visible(fn ($get) => $get('type') === 'foto')
                    ->required(fn ($get) => $get('type') === 'foto')
                    ->columnSpanFull(),
                TextInput::make('youtube_url')->label('Tautan Video YouTube')
                    ->placeholder('https://www.youtube.com/watch?v=dQw4w9WgXcQ')
                    ->helperText('Tautan video YouTube kegiatan.')
                    ->url()
                    ->visible(fn ($get) => $get('type') === 'video')
                    ->required(fn ($get) => $get('type') === 'video')
                    ->columnSpanFull(),
                Textarea::make('description')->label('Deskripsi')
                    ->placeholder('Contoh: Dokumentasi foto kegiatan gotong royong warga desa...')
                    ->helperText('Keterangan singkat mengenai dokumentasi ini.')
                    ->columnSpanFull(),
            ]);
    }
}
