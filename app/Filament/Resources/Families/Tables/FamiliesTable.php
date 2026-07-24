<?php

namespace App\Filament\Resources\Families\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\Family;

class FamiliesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ── Kolom utama (selalu tampil) ──────────────────────────────────
                TextColumn::make('kk_number')
                    ->label('No. KK')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono'),

                TextColumn::make('head_name')
                    ->label('Kepala Keluarga')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('dusun.name')
                    ->label('Dusun')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rt')
                    ->label('RT/RW')
                    ->formatStateUsing(fn ($state, Family $record) => "RT {$record->rt} / RW {$record->rw}")
                    ->sortable(),

                TextColumn::make('family_member_count')
                    ->label('Anggota')
                    ->formatStateUsing(function ($state, Family $record) {
                        $realCount = $record->citizens()->count();
                        return $realCount > 0 ? "{$realCount} Jiwa" : "{$state} Jiwa";
                    })
                    ->badge()
                    ->color(fn (Family $record) => $record->citizens()->count() > 0 ? 'success' : 'gray')
                    ->sortable(),

                // ── Kolom tambahan (tersembunyi, bisa diaktifkan) ────────────────
                TextColumn::make('head_nik')
                    ->label('NIK KK')
                    ->searchable()
                    ->placeholder('-')
                    ->copyable()
                    ->fontFamily('mono')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('photo_front')
                    ->label('Foto Rumah')
                    ->formatStateUsing(fn ($state) => empty($state) ? '-' : (str_starts_with($state, 'http') ? '🔗 Google Drive' : '🖼️ Foto Lokal'))
                    ->url(fn ($state) => empty($state) ? null : (str_starts_with($state, 'http') ? $state : asset('storage/' . $state)), shouldOpenInNewTab: true)
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('photo_kk')
                    ->label('Foto KK')
                    ->formatStateUsing(fn ($state) => empty($state) ? '-' : (str_starts_with($state, 'http') ? '🔗 Google Drive' : '🖼️ Foto Lokal'))
                    ->url(fn ($state) => empty($state) ? null : (str_starts_with($state, 'http') ? $state : asset('storage/' . $state)), shouldOpenInNewTab: true)
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
