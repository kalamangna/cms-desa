<?php

namespace App\Filament\Resources\BudgetRealizations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BudgetRealizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')->label('Kategori')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('title')->label('Judul')
                    ->searchable(),
                TextColumn::make('year')->label('Tahun')
                    ->sortable(),
                TextColumn::make('budget_amount')->label('Anggaran')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('realization_amount')->label('Realisasi')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('percentage')
                    ->label('%')
                    ->suffix('%')
                    ->getStateUsing(fn ($record) => number_format($record->percentage, 2)),
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
