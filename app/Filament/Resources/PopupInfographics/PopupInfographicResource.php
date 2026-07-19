<?php

namespace App\Filament\Resources\PopupInfographics;

use App\Filament\Resources\PopupInfographics\Pages\CreatePopupInfographic;
use App\Filament\Resources\PopupInfographics\Pages\EditPopupInfographic;
use App\Filament\Resources\PopupInfographics\Pages\ListPopupInfographics;
use App\Filament\Resources\PopupInfographics\Schemas\PopupInfographicForm;
use App\Filament\Resources\PopupInfographics\Tables\PopupInfographicsTable;
use App\Models\PopupInfographic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PopupInfographicResource extends Resource
{
    protected static ?string $model = PopupInfographic::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';
    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    public static function getModelLabel(): string
    {
        return 'Infografis';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Infografis';
    }

    public static function form(Schema $schema): Schema
    {
        return PopupInfographicForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PopupInfographicsTable::configure($table);
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
            'index' => ListPopupInfographics::route('/'),
            'create' => CreatePopupInfographic::route('/create'),
            'edit' => EditPopupInfographic::route('/{record}/edit'),
        ];
    }
}
