<?php

namespace App\Filament\Resources\VillagePotentials;

use App\Filament\Resources\VillagePotentials\Pages\CreateVillagePotential;
use App\Filament\Resources\VillagePotentials\Pages\EditVillagePotential;
use App\Filament\Resources\VillagePotentials\Pages\ListVillagePotentials;
use App\Filament\Resources\VillagePotentials\Schemas\VillagePotentialForm;
use App\Filament\Resources\VillagePotentials\Tables\VillagePotentialsTable;
use App\Models\VillagePotential;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class VillagePotentialResource extends Resource
{
    protected static ?string $model = VillagePotential::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Profil';

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return 'Potensi Desa';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Potensi Desa';
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    public static function form(Schema $schema): Schema
    {
        return VillagePotentialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VillagePotentialsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVillagePotentials::route('/'),
            'create' => CreateVillagePotential::route('/create'),
            'edit' => EditVillagePotential::route('/{record}/edit'),
        ];
    }
}
