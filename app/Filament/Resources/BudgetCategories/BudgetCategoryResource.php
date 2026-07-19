<?php

namespace App\Filament\Resources\BudgetCategories;

use App\Filament\Resources\BudgetCategories\Pages\CreateBudgetCategory;
use App\Filament\Resources\BudgetCategories\Pages\EditBudgetCategory;
use App\Filament\Resources\BudgetCategories\Pages\ListBudgetCategories;
use App\Filament\Resources\BudgetCategories\Schemas\BudgetCategoryForm;
use App\Filament\Resources\BudgetCategories\Tables\BudgetCategoriesTable;
use App\Models\BudgetCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BudgetCategoryResource extends Resource
{
    protected static ?string $model = BudgetCategory::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Master';

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return 'Kategori Anggaran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kategori Anggaran';
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    public static function form(Schema $schema): Schema
    {
        return BudgetCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BudgetCategoriesTable::configure($table);
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
            'index' => ListBudgetCategories::route('/'),
            'create' => CreateBudgetCategory::route('/create'),
            'edit' => EditBudgetCategory::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
