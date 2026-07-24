<?php

namespace App\Filament\Resources\Datasets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DatasetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ── Kolom utama ──────────────────────────────────────────────────
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('source')
                    ->label('Sumber')
                    ->searchable()
                    ->placeholder('-')
                    ->limit(25),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // ── Ketersediaan berkas (ikon ceklis) ────────────────────────────
                TextColumn::make('file_csv')
                    ->label('CSV')
                    ->formatStateUsing(fn ($state) => empty($state) ? '—' : '✓')
                    ->color(fn ($state) => empty($state) ? 'gray' : 'success')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('file_xlsx')
                    ->label('XLSX')
                    ->formatStateUsing(fn ($state) => empty($state) ? '—' : '✓')
                    ->color(fn ($state) => empty($state) ? 'gray' : 'success')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('file_pdf')
                    ->label('PDF')
                    ->formatStateUsing(fn ($state) => empty($state) ? '—' : '✓')
                    ->color(fn ($state) => empty($state) ? 'gray' : 'success')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(fn () => \App\Models\Dataset::distinct()->orderByDesc('year')->pluck('year', 'year')->toArray()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
