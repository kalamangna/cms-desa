<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')->label('Kategori')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('title')->label('Judul')
                    ->required(),
                RichEditor::make('content')->label('Konten')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('featured_image')->label('Gambar')
                    ->image()
                    ->imageResizeTargetWidth(1200)
                    ->nullable()
                    ->directory('posts'),
                DateTimePicker::make('published_at')->label('Tanggal Publikasi'),
            ]);
    }
}
