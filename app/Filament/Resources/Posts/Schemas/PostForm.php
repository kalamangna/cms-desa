<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten Berita')
                    ->description('Judul, kategori, dan isi artikel berita')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')->label('Judul')
                                    ->required()
                                    ->columnSpanFull(),
                                Select::make('category_id')->label('Kategori')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                FileUpload::make('featured_image')->label('Gambar Utama')
                                    ->image()
                                    ->imageResizeTargetWidth(1200)
                                    ->nullable()
                                    ->directory('posts'),
                            ]),
                        RichEditor::make('content')->label('Konten')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Pengaturan Publikasi')
                    ->description('Waktu tayang artikel di halaman publik')
                    ->schema([
                        DateTimePicker::make('published_at')->label('Tanggal Publikasi')
                            ->helperText('Kosongkan jika belum ingin dipublikasikan'),
                    ]),
            ]);
    }
}
