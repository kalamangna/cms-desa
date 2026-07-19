<?php

namespace App\Filament\Resources\Officials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class OfficialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama')
                    ->required(),
                TextInput::make('position')->label('Jabatan')
                    ->required(),
                Select::make('parent_id')
                    ->label('Atasan Langsung')
                    ->relationship('parent', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->position})")
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('level')
                    ->label('Tingkat Jabatan')
                    ->options([
                        1 => 'Kepala Desa (Tingkat 1)',
                        2 => 'Sekretaris Desa (Tingkat 2)',
                        3 => 'Kasi / Kaur / Kabag (Tingkat 3)',
                        4 => 'Kepala Dusun (Tingkat 4)',
                        5 => 'Staf Seksi / Staf Urusan / Staf Pendukung (Tingkat 5)',
                    ])
                    ->default(4)
                    ->required(),
                TextInput::make('order')
                    ->label('Urutan Tampilan')
                    ->numeric()
                    ->default(0)
                    ->required(),
                FileUpload::make('photo')->label('Foto')
                    ->image()
                    ->imageResizeTargetWidth(600)
                    ->nullable()
                    ->directory('officials'),
            ]);
    }
}
