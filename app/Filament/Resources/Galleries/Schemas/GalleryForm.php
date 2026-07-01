<?php

namespace App\Filament\Resources\Galleries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class GalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('Judul')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug')->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                FileUpload::make('image')->label('Gambar / Cover Thumbnail')
                    ->image()
                    ->directory('galleries')
                    ->required(),
                TextInput::make('youtube_url')->label('Link Video YouTube (Opsional)')
                    ->helperText('Contoh: https://www.youtube.com/watch?v=dQw4w9WgXcQ')
                    ->url(),
                Textarea::make('description')->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }
}
