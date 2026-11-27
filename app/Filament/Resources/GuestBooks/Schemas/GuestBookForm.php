<?php

namespace App\Filament\Resources\GuestBooks\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GuestBookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Kunjungan')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'md' => 3])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->placeholder('Budi Santoso')
                                    ->required(),
                                TextInput::make('institution_address')
                                    ->label('Instansi atau Alamat')
                                    ->placeholder('Diskominfo Kabupaten Sinjai')
                                    ->required(),
                                TextInput::make('phone')
                                    ->label('WhatsApp')
                                    ->placeholder('081234567890')
                                    ->required(),
                            ])
                            ->columnSpanFull(),
                        Textarea::make('purpose')
                            ->label('Keperluan Kunjungan')
                            ->placeholder('Koordinasi pengelolaan website desa')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
