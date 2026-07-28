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
                    ->description('Judul, kategori, dan isi artikel.')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')->label('Judul')
                            ->placeholder('Contoh: Pelaksanaan Musrenbangdes Tahun 2026')
                            ->helperText('Judul artikel yang informatif.')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('category_id')->label('Kategori')
                            ->placeholder('Pilih Kategori Artikel')
                            ->helperText('Pilih kelompok kategori berita yang sesuai.')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                        FileUpload::make('featured_image')->label('Gambar Utama')
                            ->helperText('Foto sampul artikel (JPG/PNG, 1200x630px).')
                            ->image()
                            ->imageResizeTargetWidth(1200)
                            ->nullable()
                            ->directory('posts')
                            ->columnSpanFull(),
                        RichEditor::make('content')->label('Konten')
                            ->placeholder('Tuliskan rincian isi artikel berita secara lengkap di sini...')
                            ->helperText('Isikan uraian naskah lengkap berita.')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Pengaturan Publikasi')
                    ->description('Waktu tayang artikel.')
                    ->columnSpanFull()
                    ->schema([
                        DateTimePicker::make('published_at')->label('Tanggal Publikasi')
                            ->placeholder('Kosongkan untuk terbit sekarang')
                            ->helperText('Isi untuk menjadwalkan publikasi.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
