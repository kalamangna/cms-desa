<?php

namespace App\Filament\Resources\Officials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class OfficialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')->label('Foto')
                    ->circular(),
                TextColumn::make('name')->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position')->label('Jabatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent.name')->label('Atasan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('level')->label('Tingkat')
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        1 => 'Kepala Desa',
                        2 => 'Sekretaris Desa',
                        3 => 'Kasi / Kaur',
                        4 => 'Kepala Dusun',
                        5 => 'Staf / Pendukung',
                        default => 'Lainnya',
                    })
                    ->sortable(),
                TextColumn::make('order')->label('Urutan')
                    ->sortable(),
                TextColumn::make('created_at')->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
