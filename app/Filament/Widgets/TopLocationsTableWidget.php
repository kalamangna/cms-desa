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
        $regionSql = "
            CASE region
                WHEN 'South Sulawesi' THEN 'Sulawesi Selatan'
                WHEN 'North Sulawesi' THEN 'Sulawesi Utara'
                WHEN 'Central Sulawesi' THEN 'Sulawesi Tengah'
                WHEN 'Southeast Sulawesi' THEN 'Sulawesi Tenggara'
                WHEN 'West Sulawesi' THEN 'Sulawesi Barat'
                WHEN 'Special Region of Yogyakarta' THEN 'DI Yogyakarta'
                WHEN 'Yogyakarta' THEN 'DI Yogyakarta'
                WHEN 'Jakarta' THEN 'DKI Jakarta'
                WHEN 'Special Capital Region of Jakarta' THEN 'DKI Jakarta'
                WHEN 'West Java' THEN 'Jawa Barat'
                WHEN 'Central Java' THEN 'Jawa Tengah'
                WHEN 'East Java' THEN 'Jawa Timur'
                WHEN 'West Nusa Tenggara' THEN 'Nusa Tenggara Barat'
                WHEN 'East Nusa Tenggara' THEN 'Nusa Tenggara Timur'
                WHEN 'West Kalimantan' THEN 'Kalimantan Barat'
                WHEN 'South Kalimantan' THEN 'Kalimantan Selatan'
                WHEN 'Central Kalimantan' THEN 'Kalimantan Tengah'
                WHEN 'East Kalimantan' THEN 'Kalimantan Timur'
                WHEN 'North Kalimantan' THEN 'Kalimantan Utara'
                WHEN 'North Sumatra' THEN 'Sumatera Utara'
                WHEN 'West Sumatra' THEN 'Sumatera Barat'
                WHEN 'South Sumatra' THEN 'Sumatera Selatan'
                WHEN 'Riau Islands' THEN 'Kepulauan Riau'
                WHEN 'Bangka Belitung' THEN 'Kepulauan Bangka Belitung'
                WHEN 'Bangka-Belitung Islands' THEN 'Kepulauan Bangka Belitung'
                WHEN 'North Maluku' THEN 'Maluku Utara'
                WHEN 'West Papua' THEN 'Papua Barat'
                ELSE COALESCE(region, '-')
            END
        ";

        return $table
            ->query(
                VisitorLog::query()
                    ->select(
                        DB::raw('min(id) as id'),
                        DB::raw("COALESCE(city, 'Lokal / Tidak Terdeteksi') as city_name"),
                        DB::raw("{$regionSql} as region_name"),
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
