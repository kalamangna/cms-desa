<?php

namespace App\Filament\Resources\Officials;

use App\Filament\Resources\Officials\Pages\CreateOfficial;
use App\Filament\Resources\Officials\Pages\EditOfficial;
use App\Filament\Resources\Officials\Pages\ListOfficials;
use App\Filament\Resources\Officials\Schemas\OfficialForm;
use App\Filament\Resources\Officials\Tables\OfficialsTable;
use App\Models\Official;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfficialResource extends Resource
{
    protected static ?string $model = Official::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Pemerintahan';

    public static function getModelLabel(): string
    {
        return 'Perangkat Desa';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Perangkat Desa';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return OfficialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OfficialsTable::configure($table);
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
            'index' => ListOfficials::route('/'),
            'create' => CreateOfficial::route('/create'),
            'edit' => EditOfficial::route('/{record}/edit'),
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
