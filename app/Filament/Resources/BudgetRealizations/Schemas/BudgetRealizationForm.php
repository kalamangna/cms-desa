<?php

namespace App\Filament\Resources\BudgetRealizations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class BudgetRealizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('budget_category_id')->label('Kategori')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('title')->label('Judul')
                    ->required(),
                TextInput::make('year')->label('Tahun')
                    ->numeric()
                    ->required(),
                TextInput::make('budget_amount')->label('Anggaran')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                TextInput::make('realization_amount')->label('Realisasi')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
            ]);
    }
}
