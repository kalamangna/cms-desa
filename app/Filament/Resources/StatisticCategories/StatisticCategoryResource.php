<?php

namespace App\Filament\Resources\StatisticCategories;

use App\Filament\Resources\StatisticCategories\Pages\CreateStatisticCategory;
use App\Filament\Resources\StatisticCategories\Pages\EditStatisticCategory;
use App\Filament\Resources\StatisticCategories\Pages\ListStatisticCategories;
use App\Filament\Resources\StatisticCategories\Schemas\StatisticCategoryForm;
use App\Filament\Resources\StatisticCategories\Tables\StatisticCategoriesTable;
use App\Models\StatisticCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StatisticCategoryResource extends Resource
{
    protected static ?string $model = StatisticCategory::class;

    protected static $navigationGroup = 'Data Master';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return StatisticCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StatisticCategoriesTable::configure($table);
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
            'index' => ListStatisticCategories::route('/'),
            'create' => CreateStatisticCategory::route('/create'),
            'edit' => EditStatisticCategory::route('/{record}/edit'),
        ];
    }
}
