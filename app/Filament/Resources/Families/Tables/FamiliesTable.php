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
                TextColumn::make('family_member_count')->label('Jumlah Anggota')->sortable(),
                TextColumn::make('assistance_type')->label('Bansos')->searchable(),
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
