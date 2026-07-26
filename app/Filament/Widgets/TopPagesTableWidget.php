<?php

namespace App\Filament\Widgets;

use App\Models\VisitorLog;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class TopPagesTableWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;
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
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('url')
                    ->label('URL Halaman')
                    ->searchable()
                    ->wrap()
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
            ->filters([
                Tables\Filters\SelectFilter::make('period')
                    ->label('Periode Waktu')
                    ->options([
                        '7' => '7 Hari Terakhir',
                        '30' => '30 Hari Terakhir',
                        'all' => 'Semua Waktu',
                    ])
                    ->default('all')
                    ->query(function ($query, array $data) {
                        $period = $data['value'] ?? 'all';
                        if ($period === '7') {
                            $query->where('visit_date', '>=', now()->subDays(7)->toDateString());
                        } elseif ($period === '30') {
                            $query->where('visit_date', '>=', now()->subDays(30)->toDateString());
                        }
                    }),
            ])
            ->paginated(false);
    }
}
