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
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')->label('Deskripsi Singkat')
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('requirements')->label('Persyaratan / Prosedur')
                    ->columnSpanFull(),
            ]);
    }
}
