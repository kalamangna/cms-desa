<?php

namespace App\Filament\Resources\BudgetRealizations;

use App\Filament\Resources\BudgetRealizations\Pages\CreateBudgetRealization;
use App\Filament\Resources\BudgetRealizations\Pages\EditBudgetRealization;
use App\Filament\Resources\BudgetRealizations\Pages\ListBudgetRealizations;
use App\Filament\Resources\BudgetRealizations\Schemas\BudgetRealizationForm;
use App\Filament\Resources\BudgetRealizations\Tables\BudgetRealizationsTable;
use App\Models\BudgetRealization;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BudgetRealizationResource extends Resource
{
    protected static ?string $model = BudgetRealization::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Transparansi';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return 'APBDes';
    }

    public static function getPluralModelLabel(): string
    {
        return 'APBDes';
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Schema $schema): Schema
    {
        return BudgetRealizationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BudgetRealizationsTable::configure($table);
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
            'index' => ListBudgetRealizations::route('/'),
            'create' => CreateBudgetRealization::route('/create'),
            'edit' => EditBudgetRealization::route('/{record}/edit'),
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
