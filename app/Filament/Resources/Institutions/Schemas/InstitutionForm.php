<?php

namespace App\Filament\Resources\Institutions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class InstitutionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lembaga')
                    ->placeholder('Contoh: BPD (Badan Permusyawaratan Desa) atau Karang Taruna')
                    ->helperText('Nama resmi lembaga kemasyarakatan desa.')
                    ->required(),
                FileUpload::make('logo')
                    ->label('Logo Lembaga')
                    ->helperText('Unggah logo resmi lembaga (Maksimal 2MB).')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->imageResizeTargetWidth(400)
                    ->maxSize(2048)
                    ->nullable()
                    ->directory('institutions')
                    ->columnSpanFull(),
            ]);
    }
}
