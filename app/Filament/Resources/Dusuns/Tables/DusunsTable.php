<?php

namespace App\Filament\Resources\Dusuns\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;

class DusunsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Dusun')->searchable()->sortable(),
                TextColumn::make('head_name')->label('Kepala Dusun')->searchable()->sortable(),
                TextColumn::make('total_rt')->label('RT')->sortable(),
                TextColumn::make('total_rw')->label('RW')->sortable(),
                TextColumn::make('citizens_count')->label('Jumlah Penduduk')
                    ->counts('citizens')
                    ->sortable(),
                TextColumn::make('geojson')
                    ->label('Status Spasial')
                    ->state(fn ($record) => $record->geojson ? 'Terpetakan' : 'Belum')
                    ->badge()
                    ->color(fn ($state) => $state === 'Terpetakan' ? 'success' : 'gray'),
                TextColumn::make('created_at')->label('Dibuat')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
