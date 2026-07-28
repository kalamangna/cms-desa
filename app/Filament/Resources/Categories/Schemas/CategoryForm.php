<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama Kategori')
                    ->placeholder('Contoh: Berita Utama atau Program Desa')
                    ->helperText('Nama kelompok kategori artikel berita.')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
