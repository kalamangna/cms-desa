<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $map = [
            'South Sulawesi' => 'Sulawesi Selatan',
            'North Sulawesi' => 'Sulawesi Utara',
            'Central Sulawesi' => 'Sulawesi Tengah',
            'Southeast Sulawesi' => 'Sulawesi Tenggara',
            'West Sulawesi' => 'Sulawesi Barat',
            'Special Region of Yogyakarta' => 'DI Yogyakarta',
            'Yogyakarta' => 'DI Yogyakarta',
            'Jakarta' => 'DKI Jakarta',
            'Special Capital Region of Jakarta' => 'DKI Jakarta',
            'West Java' => 'Jawa Barat',
            'Central Java' => 'Jawa Tengah',
            'East Java' => 'Jawa Timur',
            'Banten' => 'Banten',
            'Bali' => 'Bali',
            'West Nusa Tenggara' => 'Nusa Tenggara Barat',
            'East Nusa Tenggara' => 'Nusa Tenggara Timur',
            'West Kalimantan' => 'Kalimantan Barat',
            'South Kalimantan' => 'Kalimantan Selatan',
            'Central Kalimantan' => 'Kalimantan Tengah',
            'East Kalimantan' => 'Kalimantan Timur',
            'North Kalimantan' => 'Kalimantan Utara',
            'North Sumatra' => 'Sumatera Utara',
            'West Sumatra' => 'Sumatera Barat',
            'South Sumatra' => 'Sumatera Selatan',
            'Aceh' => 'Aceh',
            'Riau' => 'Riau',
            'Riau Islands' => 'Kepulauan Riau',
            'Jambi' => 'Jambi',
            'Bengkulu' => 'Bengkulu',
            'Lampung' => 'Lampung',
            'Bangka Belitung' => 'Kepulauan Bangka Belitung',
            'Bangka-Belitung Islands' => 'Kepulauan Bangka Belitung',
            'Maluku' => 'Maluku',
            'North Maluku' => 'Maluku Utara',
            'Papua' => 'Papua',
            'West Papua' => 'Papua Barat',
        ];

        foreach ($map as $english => $indonesian) {
            DB::table('visitor_logs')
                ->where('region', $english)
                ->update(['region' => $indonesian]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed for data cleanup
    }
};
