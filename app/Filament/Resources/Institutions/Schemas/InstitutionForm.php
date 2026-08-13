<?php

namespace App\Filament\Resources\Institutions\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class InstitutionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lembaga')
                            ->placeholder('Contoh: BPD (Badan Permusyawaratan Desa) atau Karang Taruna')
                            ->helperText('Nama resmi lembaga kemasyarakatan desa.')
                            ->required(),
                        FileUpload::make('logo')
                            ->label('Logo Lembaga')
                            ->helperText('Unggah logo resmi lembaga (Maksimal 2MB).')
                            ->disk('public')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->imageResizeTargetWidth(400)
                            ->maxSize(2048)
                            ->nullable()
                            ->directory('institutions'),
                    ])
                    ->columnSpanFull(),
                RichEditor::make('description')
                    ->label('Deskripsi / Profil Lembaga')
                    ->helperText('Jelaskan profil, visi, misi, atau peran lembaga ini di desa.')
                    ->nullable()
                    ->columnSpanFull(),
                Repeater::make('management')
                    ->label('Struktur Pengurus')
                    ->helperText('Daftar pengurus lembaga.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('position')
                                    ->label('Jabatan')
                                    ->placeholder('Contoh: Ketua')
                                    ->required(),
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->placeholder('Nama pengurus')
                                    ->required(),
                            ]),
                    ])
                    ->collapsible()
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ]);
    }
}
