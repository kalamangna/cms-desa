<?php

namespace App\Filament\Components;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ShuvroRoy\FilamentSpatieLaravelBackup\Components\BackupDestinationListRecords as BaseComponent;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackup;

class CustomBackupDestinationListRecords extends BaseComponent
{
    public function table(Table $table): Table
    {
        $table = parent::table($table);

        return $table
            ->defaultSort('date', 'desc')
            ->records(
                function (?string $sortColumn, ?string $sortDirection, ?string $search) {
                    $data = [];

                    foreach (FilamentSpatieLaravelBackup::getDisks() as $disk) {
                        $data = array_merge($data, FilamentSpatieLaravelBackup::getBackupDestinationData($disk));
                    }

                    $sortCol = $sortColumn ?: 'date';
                    $sortDir = $sortDirection ?: 'desc';

                    return collect($data)
                        ->sortBy(
                            $sortCol,
                            SORT_NATURAL,
                            $sortDir === 'desc',
                        )
                        ->when(
                            filled($search),
                            fn (Collection $data): Collection => $data->filter(
                                fn (array $record): bool => Str::contains(
                                    Str::lower($record['path'] . $record['disk'] . $record['date']),
                                    Str::lower($search),
                                ),
                            ),
                        );
                }
            )
            ->columns([
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
