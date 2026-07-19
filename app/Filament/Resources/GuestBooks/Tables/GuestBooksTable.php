<?php

namespace App\Filament\Resources\GuestBooks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class GuestBooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('institution_address')->label('Instansi / Alamat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')->label('Nomor Kontak')
                    ->searchable(),
                TextColumn::make('purpose')->label('Keperluan / Pesan')
                    ->limit(50),
                TextColumn::make('created_at')->label('Tanggal Kunjungan')
                    ->dateTime()
                    ->sortable(),
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
