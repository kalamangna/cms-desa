<?php

namespace App\Filament\Resources\PublicFacilities;

use App\Filament\Resources\PublicFacilities\Pages\CreatePublicFacility;
use App\Filament\Resources\PublicFacilities\Pages\EditPublicFacility;
use App\Filament\Resources\PublicFacilities\Pages\ListPublicFacilities;
use App\Filament\Resources\PublicFacilities\Schemas\PublicFacilityForm;
use App\Filament\Resources\PublicFacilities\Tables\PublicFacilitiesTable;
use App\Models\PublicFacility;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PublicFacilityResource extends Resource
{
    protected static ?string $model = PublicFacility::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';
    protected static string|\UnitEnum|null $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'Fasilitas Umum';
    protected static ?int $navigationSort = 6;

    public static function getModelLabel(): string
    {
        return 'Fasilitas Umum';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Fasilitas Umum';
    }

    public static function form(Schema $schema): Schema
    {
        return PublicFacilityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PublicFacilitiesTable::configure($table);
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
            'index' => ListPublicFacilities::route('/'),
            'create' => CreatePublicFacility::route('/create'),
            'edit' => EditPublicFacility::route('/{record}/edit'),
        ];
    }
}
