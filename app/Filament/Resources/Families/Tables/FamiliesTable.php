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
                TextColumn::make('kk_number')->label('No. KK')->searchable()->sortable(),
                TextColumn::make('head_name')->label('Kepala Keluarga')->searchable()->sortable(),
                TextColumn::make('dusun.name')->label('Dusun')->searchable()->sortable(),
                TextColumn::make('rt')->label('RT')->sortable(),
                TextColumn::make('rw')->label('RW')->sortable(),
                TextColumn::make('family_member_count')
                    ->label('Jumlah Anggota')
                    ->formatStateUsing(function ($state, Family $record) {
                        $realCount = $record->citizens()->count();
                        if ($realCount > 0) {
                            return "{$realCount} Jiwa";
                        }
                        return "{$state} Jiwa";
                    })
                    ->badge()
                    ->color(fn (Family $record) => $record->citizens()->count() > 0 ? 'success' : 'gray')
                    ->sortable(),
                TextColumn::make('assistance_type')->label('Bansos')->searchable(),
                TextColumn::make('cow_count')->label('Sapi')->sortable()->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('goat_count')->label('Kambing')->sortable()->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('buffalo_count')->label('Kerbau')->sortable()->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('photo_kk')
                    ->label('Foto KK')
                    ->formatStateUsing(fn ($state) => empty($state) ? '-' : (str_starts_with($state, 'http') ? '🔗 Google Drive' : '🖼️ Foto Lokal'))
                    ->url(fn ($state) => empty($state) ? null : (str_starts_with($state, 'http') ? $state : asset('storage/' . $state)), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('photo_front')
                    ->label('Foto Rumah')
                    ->formatStateUsing(fn ($state) => empty($state) ? '-' : (str_starts_with($state, 'http') ? '🔗 Google Drive' : '🖼️ Foto Lokal'))
                    ->url(fn ($state) => empty($state) ? null : (str_starts_with($state, 'http') ? $state : asset('storage/' . $state)), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')->label('Dibuat')->dateTime()->toggleable(isToggledHiddenByDefault: true),
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
