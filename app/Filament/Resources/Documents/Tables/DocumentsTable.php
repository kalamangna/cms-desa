<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Models\Document;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Judul')
                    ->searchable(),
                TextColumn::make('category')->label('Jenis Dokumen')
                    ->formatStateUsing(fn (?string $state): string => Document::CATEGORY_OPTIONS[$state] ?? '-')
                    ->badge()
                    ->sortable(),
                TextColumn::make('information_classification')->label('Klasifikasi Informasi')
                    ->formatStateUsing(fn (?string $state): string => Document::INFORMATION_CLASSIFICATION_OPTIONS[$state] ?? '-')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'berkala' => 'success',
                        'serta_merta' => 'warning',
                        'setiap_saat' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('document_number')->label('Nomor Dokumen')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('year')->label('Tahun')
                    ->sortable(),
                TextColumn::make('slug')->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('file')->label('Berkas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')->label('Dihapus')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Jenis Dokumen')
                    ->options(Document::CATEGORY_OPTIONS),
                SelectFilter::make('information_classification')
                    ->label('Klasifikasi Informasi')
                    ->options(Document::INFORMATION_CLASSIFICATION_OPTIONS),
                SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(fn (): array => Document::query()
                        ->whereNotNull('year')
                        ->distinct()
                        ->orderByDesc('year')
                        ->pluck('year', 'year')
                        ->mapWithKeys(fn ($y): array => [(string) $y => (string) $y])
                        ->all()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
