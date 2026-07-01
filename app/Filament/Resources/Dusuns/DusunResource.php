<?php

namespace App\Filament\Resources\Dusuns;

use App\Filament\Resources\Dusuns\Pages\CreateDusun;
use App\Filament\Resources\Dusuns\Pages\EditDusun;
use App\Filament\Resources\Dusuns\Pages\ListDusuns;
use App\Filament\Resources\Dusuns\Schemas\DusunForm;
use App\Filament\Resources\Dusuns\Tables\DusunsTable;
use App\Models\Dusun;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DusunResource extends Resource
{
    protected static ?string $model = Dusun::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Kependudukan';

    protected static ?string $navigationLabel = 'Wilayah Dusun';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return 'Dusun';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Dusun';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DusunForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DusunsTable::configure($table);
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
            'index' => ListDusuns::route('/'),
            'create' => CreateDusun::route('/create'),
            'edit' => EditDusun::route('/{record}/edit'),
        ];
    }
}
