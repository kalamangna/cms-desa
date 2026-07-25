<?php

namespace App\Filament\Widgets;

use App\Models\VisitorLog;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class TopPagesTableWidget extends BaseWidget
{
    protected static ?string $heading = '10 Halaman Paling Sering Dikunjungi';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                VisitorLog::query()
                    ->select(DB::raw('min(id) as id'), 'url', DB::raw('count(*) as total_views'), DB::raw('count(distinct ip_hash) as unique_visitors'))
                    ->groupBy('url')
                    ->orderByDesc('total_views')
            )
            ->columns([
                Tables\Columns\TextColumn::make('url')
                    ->label('URL Halaman')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => parse_url($state, PHP_URL_PATH) ?: '/')
                    ->url(fn ($record) => $record->url, true),

                Tables\Columns\TextColumn::make('total_views')
                    ->label('Total Dilihat (Views)')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('unique_visitors')
                    ->label('Pengunjung Unik')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),
            ])
            ->paginated(false)
            ->defaultPaginationPageOption(10);
    }
}
