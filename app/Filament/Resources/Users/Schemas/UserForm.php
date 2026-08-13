<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('name')->label('Nama Lengkap')
                            ->placeholder('Contoh: Administrator Utama')
                            ->helperText('Nama pengelola akun.')
                            ->required(),
                        TextInput::make('username')->label('Username')
                            ->placeholder('Contoh: admin')
                            ->helperText('Nama pengguna untuk masuk sistem.')
                            ->required(),
                    ])
                    ->columnSpanFull(),
                Grid::make(2)
                    ->schema([
                        Select::make('roles')->label('Peran (Role)')
                            ->placeholder('Pilih Peran')
                            ->helperText('Hak akses pengguna.')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->required(),
                        TextInput::make('password')->label('Kata Sandi')
                            ->placeholder('••••••••')
                            ->helperText('Kosongkan jika sandi tidak diubah.')
                            ->password()
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
