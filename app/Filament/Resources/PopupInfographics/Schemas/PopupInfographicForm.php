<?php

namespace App\Filament\Resources\PopupInfographics\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;

class PopupInfographicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul / Deskripsi Singkat')
                    ->placeholder('Misal: Poster Lomba HUT RI')
                    ->helperText('Judul singkat poster atau infografis.')
                    ->nullable()
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Gambar Poster / Infografis')
                    ->helperText('Unggah gambar poster/infografis (Otomatis dioptimalkan, maks 2MB).')
                    ->image()
                    ->imageResizeTargetWidth(1200)
                    ->maxSize(2048)
                    ->required()
                    ->directory('settings')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Urutan Muncul')
                    ->placeholder('Contoh: 1')
                    ->helperText('Urutan tampil (terkecil = pertama).')
                    ->numeric()
                    ->default(fn () => (\App\Models\PopupInfographic::max('sort_order') ?? 0) + 1)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
