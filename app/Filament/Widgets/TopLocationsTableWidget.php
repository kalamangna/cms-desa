<?php

namespace App\Filament\Widgets;

use App\Models\VisitorLog;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class TopLocationsTableWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;
    protected static ?string $heading = 'Sebaran Lokasi Pengunjung';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                VisitorLog::query()
                    ->select(
                        DB::raw('min(id) as id'),
                        DB::raw("COALESCE(city, 'Lokal / Tidak Terdeteksi') as city_name"),
                        DB::raw("COALESCE(region, '-') as region_name"),
                        DB::raw("COALESCE(country, 'Indonesia') as country_name"),
                        DB::raw('count(distinct ip_hash) as unique_visitors'),
                        DB::raw('count(*) as total_views')
                    )
                    ->groupBy('city_name', 'region_name', 'country_name')
                    ->orderByDesc('unique_visitors')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('city_name')
                    ->label('Kota / Kabupaten')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('region_name')
                    ->label('Provinsi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('country_name')
                    ->label('Negara')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unique_visitors')
                    ->label('Pengunjung Unik')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_views')
                    ->label('Total Dilihat (Views)')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),
            ])
            ->paginated(false);
    }
}
