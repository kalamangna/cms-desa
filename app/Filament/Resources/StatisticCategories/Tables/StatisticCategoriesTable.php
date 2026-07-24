<?php

namespace App\Filament\Resources\StatisticCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StatisticCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mapping_table')
                    ->label('Sumber Data')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'citizens' => 'Data Penduduk (Individu)',
                        'families' => 'Data Keluarga',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'citizens' => 'info',
                        'families' => 'warning',
                        default => 'gray',
                    }),
                IconColumn::make('is_active')
                    ->label('Publik')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('indicators_count')
                    ->label('Jumlah Indikator')
                    ->counts('indicators'),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('mapping_table')
                    ->label('Sumber Data')
                    ->options([
                        'citizens' => 'Data Penduduk',
                        'families' => 'Data Keluarga',
                    ]),
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
