<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('Nama Layanan')
                    ->required(),
                TextInput::make('icon')->label('Ikon (FontAwesome)')
                    ->helperText('Contoh: fa-users, fa-id-card, fa-baby, fa-heart, fa-house-user, fa-hand-holding-heart')
                    ->default('fa-circle-info')
                    ->required(),
                Textarea::make('description')->label('Deskripsi Singkat')
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('requirements')->label('Persyaratan / Prosedur')
                    ->columnSpanFull(),
            ]);
    }
}
