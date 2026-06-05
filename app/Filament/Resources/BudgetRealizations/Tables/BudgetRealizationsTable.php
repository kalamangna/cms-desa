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
                TextColumn::make('category.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('year')
                    ->sortable(),
                TextColumn::make('budget_amount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('realization_amount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('percentage')
                    ->label('%')
                    ->suffix('%')
                    ->getStateUsing(fn ($record) => number_format($record->percentage, 2)),
                TextColumn::make('created_at')
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
