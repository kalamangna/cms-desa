<?php

namespace App\Filament\Resources\StatisticData\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class StatisticDataForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('statistic_indicator_id')->label('Indikator')
                    ->relationship('indicator', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('year')->label('Tahun')
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100)
                    ->required(),
                TextInput::make('value')->label('Nilai')
                    ->numeric()
                    ->required(),
            ]);
    }
}
