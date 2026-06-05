<?php

namespace App\Filament\Resources\Metadata;

use App\Filament\Resources\Metadata\Pages\CreateMetadata;
use App\Filament\Resources\Metadata\Pages\EditMetadata;
use App\Filament\Resources\Metadata\Pages\ListMetadata;
use App\Filament\Resources\Metadata\Schemas\MetadataForm;
use App\Filament\Resources\Metadata\Tables\MetadataTable;
use App\Models\Metadata;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MetadataResource extends Resource
{
    protected static ?string $model = Metadata::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Master';

    public static function getModelLabel(): string
    {
        return 'Metadata';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Metadata';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MetadataForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MetadataTable::configure($table);
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
            'index' => ListMetadata::route('/'),
            'create' => CreateMetadata::route('/create'),
            'edit' => EditMetadata::route('/{record}/edit'),
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
