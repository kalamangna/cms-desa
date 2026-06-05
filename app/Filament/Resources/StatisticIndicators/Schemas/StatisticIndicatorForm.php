<?php

namespace App\Filament\Resources\StatisticIndicators\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class StatisticIndicatorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('statistic_category_id')->label('Kategori')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('name')->label('Nama')
                    ->required(),
                TextInput::make('unit')
                    ->placeholder('e.g., Orang, Jiwa, Persen'),
            ]);
    }
}
