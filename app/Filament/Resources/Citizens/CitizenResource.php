<?php

namespace App\Filament\Resources\Citizens;

use App\Filament\Resources\Citizens\Pages\CreateCitizen;
use App\Filament\Resources\Citizens\Pages\EditCitizen;
use App\Filament\Resources\Citizens\Pages\ListCitizens;
use App\Filament\Resources\Citizens\Schemas\CitizenForm;
use App\Filament\Resources\Citizens\Tables\CitizensTable;
use App\Models\Citizen;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CitizenResource extends Resource
{
    protected static ?string $model = Citizen::class;

    protected static ?string $modelLabel = 'Penduduk / Warga';

    protected static ?string $pluralModelLabel = 'Data Penduduk';

    protected static string|\UnitEnum|null $navigationGroup = 'Kependudukan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function form(Schema $schema): Schema
    {
        return CitizenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CitizensTable::configure($table);
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
            'index' => ListCitizens::route('/'),
            'create' => CreateCitizen::route('/create'),
            'edit' => EditCitizen::route('/{record}/edit'),
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
