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
        // Set static data for Province and Regency in the settings table
        DB::table('settings')->updateOrInsert(
            ['key' => 'province_name'],
            ['value' => 'SULAWESI SELATAN', 'updated_at' => now()]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'regency_name'],
            ['value' => 'SINJAI', 'updated_at' => now()]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'province_name'],
            ['value' => 'Nama Provinsi', 'updated_at' => now()]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'regency_name'],
            ['value' => 'Nama Kabupaten', 'updated_at' => now()]
        );
    }
};
