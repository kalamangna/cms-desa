<?php

namespace App\Filament\Resources\Citizens\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CitizensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nik')->label('NIK')
                    ->searchable(),
                TextColumn::make('kk_number')->label('No. KK')
                    ->searchable(),
                TextColumn::make('name')->label('Nama')
                    ->searchable(),
                TextColumn::make('place_of_birth')->label('Tempat Lahir')
                    ->searchable(),
                TextColumn::make('date_of_birth')->label('Tanggal Lahir')
                    ->date()
                    ->sortable(),
                TextColumn::make('gender')->label('Jenis Kelamin')
                    ->searchable(),
                TextColumn::make('religion')->label('Agama')
                    ->searchable(),
                TextColumn::make('education')->label('Pendidikan')
                    ->searchable(),
                TextColumn::make('job')->label('Pekerjaan')
                    ->searchable(),
                TextColumn::make('blood_type')->label('Gol. Darah')
                    ->searchable(),
                TextColumn::make('marital_status')->label('Status Kawin')
                    ->searchable(),
                TextColumn::make('rt')->label('RT')
                    ->searchable(),
                TextColumn::make('rw')->label('RW')
                    ->searchable(),
                TextColumn::make('status')->label('Status')
                    ->searchable(),
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
