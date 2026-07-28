<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        TextInput::make('name')->label('Nama')
                            ->required(),
                        TextInput::make('username')->label('Username')
                            ->required(),
                    ])
                    ->columnSpanFull(),
                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        \Filament\Forms\Components\Select::make('roles')->label('Peran (Role)')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->required(),
                        TextInput::make('password')->label('Kata Sandi')
                            ->password()
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
