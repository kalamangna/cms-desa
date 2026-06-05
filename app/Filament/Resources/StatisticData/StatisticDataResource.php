<?php

namespace App\Filament\Resources\StatisticData;

use App\Filament\Resources\StatisticData\Pages\CreateStatisticData;
use App\Filament\Resources\StatisticData\Pages\EditStatisticData;
use App\Filament\Resources\StatisticData\Pages\ListStatisticData;
use App\Filament\Resources\StatisticData\Schemas\StatisticDataForm;
use App\Filament\Resources\StatisticData\Tables\StatisticDataTable;
use App\Models\StatisticData;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StatisticDataResource extends Resource
{
    protected static ?string $model = StatisticData::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Data';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return 'Data Statistik';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Statistik';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return StatisticDataForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StatisticDataTable::configure($table);
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
            'index' => ListStatisticData::route('/'),
            'create' => CreateStatisticData::route('/create'),
            'edit' => EditStatisticData::route('/{record}/edit'),
        ];
    }
}
