<?php

namespace App\Filament\Resources\GuestBooks;

use App\Filament\Resources\GuestBooks\Pages\CreateGuestBook;
use App\Filament\Resources\GuestBooks\Pages\EditGuestBook;
use App\Filament\Resources\GuestBooks\Pages\ListGuestBooks;
use App\Filament\Resources\GuestBooks\Schemas\GuestBookForm;
use App\Filament\Resources\GuestBooks\Tables\GuestBooksTable;
use App\Models\GuestBook;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GuestBookResource extends Resource
{
    protected static ?string $model = GuestBook::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static string|\UnitEnum|null $navigationGroup = 'Layanan';
    protected static ?string $navigationLabel = 'Buku Tamu';
    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return 'Buku Tamu';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Buku Tamu';
    }

    public static function form(Schema $schema): Schema
    {
        return GuestBookForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuestBooksTable::configure($table);
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
            'index' => ListGuestBooks::route('/'),
            'create' => CreateGuestBook::route('/create'),
            'edit' => EditGuestBook::route('/{record}/edit'),
        ];
    }
}
