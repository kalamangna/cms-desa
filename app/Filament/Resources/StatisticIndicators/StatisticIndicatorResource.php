<?php

namespace App\Filament\Resources\StatisticIndicators;

use App\Filament\Resources\StatisticIndicators\Pages\CreateStatisticIndicator;
use App\Filament\Resources\StatisticIndicators\Pages\EditStatisticIndicator;
use App\Filament\Resources\StatisticIndicators\Pages\ListStatisticIndicators;
use App\Filament\Resources\StatisticIndicators\Schemas\StatisticIndicatorForm;
use App\Filament\Resources\StatisticIndicators\Tables\StatisticIndicatorsTable;
use App\Models\StatisticIndicator;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StatisticIndicatorResource extends Resource
{
    protected static ?string $model = StatisticIndicator::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Data';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return 'Statistik';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Statistik';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return StatisticIndicatorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StatisticIndicatorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStatisticIndicators::route('/'),
            'create' => CreateStatisticIndicator::route('/create'),
            'edit' => EditStatisticIndicator::route('/{record}/edit'),
        ];
    }
}
