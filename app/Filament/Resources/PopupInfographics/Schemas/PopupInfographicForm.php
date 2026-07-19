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
                    ->nullable()
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Gambar Poster / Infografis')
                    ->image()
                    ->required()
                    ->directory('settings')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Urutan Muncul')
                    ->numeric()
                    ->default(fn () => (\App\Models\PopupInfographic::max('sort_order') ?? 0) + 1)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
