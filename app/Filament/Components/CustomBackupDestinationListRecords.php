<?php

namespace App\Filament\Components;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use ShuvroRoy\FilamentSpatieLaravelBackup\Components\BackupDestinationListRecords as BaseComponent;

class CustomBackupDestinationListRecords extends BaseComponent
{
    public function table(Table $table): Table
    {
        $table = parent::table($table);

        return $table->columns([
            TextColumn::make('path')
                ->label(__('filament-spatie-backup::backup.components.backup_destination_list.table.fields.path'))
                ->searchable()
                ->sortable(),
            TextColumn::make('disk')
                ->label(__('filament-spatie-backup::backup.components.backup_destination_list.table.fields.disk'))
                ->searchable()
                ->sortable(),
            TextColumn::make('date')
                ->label(__('filament-spatie-backup::backup.components.backup_destination_list.table.fields.date'))
                ->formatStateUsing(function ($state) {
                    if (! $state) {
                        return '-';
                    }

                    return Carbon::parse($state, 'UTC')
                        ->setTimezone(config('app.timezone', 'Asia/Makassar'))
                        ->translatedFormat('d M Y, H:i:s \W\I\T\A');
                })
                ->searchable()
                ->sortable(),
            TextColumn::make('size')
                ->label(__('filament-spatie-backup::backup.components.backup_destination_list.table.fields.size'))
                ->badge(),
        ]);
    }
}
