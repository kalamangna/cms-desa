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
        // Standarisasi penulisan Jenis Bangunan Tempat Tinggal (building_type)
        DB::table('families')
            ->whereIn('building_type', ['Rumah tinggal tunggal', 'rumah tinggal tunggal', 'Rumah Tinggal Tunggal'])
            ->update(['building_type' => 'Rumah Tinggal Tunggal']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op demi integritas data
    }
};
