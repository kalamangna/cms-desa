<?php

namespace App\Filament\Resources\PublicFacilities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PublicFacilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Fasilitas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')->label('Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('latitude')->label('Latitude'),
                TextColumn::make('longitude')->label('Longitude'),
                TextColumn::make('created_at')->label('Dibuat')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
