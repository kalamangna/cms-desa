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
                TextInput::make('title')->label('Judul')
                    ->required(),
                Select::make('type')->label('Tipe Galeri')
                    ->options([
                        'foto' => 'Foto',
                        'video' => 'Video',
                    ])
                    ->default('foto')
                    ->required()
                    ->live(),
                FileUpload::make('image')->label('Foto')
                    ->image()
                    ->directory('galleries')
                    ->visible(fn ($get) => $get('type') === 'foto')
                    ->required(fn ($get) => $get('type') === 'foto'),
                TextInput::make('youtube_url')->label('Tautan Video YouTube')
                    ->helperText('Contoh: https://www.youtube.com/watch?v=dQw4w9WgXcQ')
                    ->url()
                    ->visible(fn ($get) => $get('type') === 'video')
                    ->required(fn ($get) => $get('type') === 'video'),
                Textarea::make('description')->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }
}
