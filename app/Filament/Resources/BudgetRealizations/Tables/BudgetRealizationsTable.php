<?php

namespace App\Filament\Resources\BudgetRealizations\Tables;

use App\Models\BudgetRealization;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(fn () => BudgetRealization::distinct()->orderByDesc('year')->pluck('year', 'year')->toArray()),
                SelectFilter::make('budget_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
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
