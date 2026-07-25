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
        // 1. Standarisasi Sumber Air Bersih (water_source)
        DB::table('families')
            ->whereIn('water_source', ['Sumur bor/pompa', 'sumur bor/pompa', 'Sumur Bor/Pompa'])
            ->update(['water_source' => 'Sumur Bor / Pompa']);

        DB::table('families')
            ->whereIn('water_source', ['Sumur terlindung', 'sumur terlindung', 'Sumur Terlindung'])
            ->update(['water_source' => 'Sumur Terlindung']);

        DB::table('families')
            ->whereIn('water_source', ['Air kemasan bermerek', 'air kemasan bermerek'])
            ->update(['water_source' => 'Air Kemasan Bermerek']);

        // 2. Standarisasi Tempat Pembuangan Akhir Tinja (feces_disposal)
        DB::table('families')
            ->whereIn('feces_disposal', ['Tangki septik', 'tangki septik'])
            ->update(['feces_disposal' => 'Tangki Septik']);

        DB::table('families')
            ->whereIn('feces_disposal', ['Lubang tanah', 'lubang tanah'])
            ->update(['feces_disposal' => 'Lubang Tanah']);

        // 3. Standarisasi Jenis Kloset (closet_type)
        DB::table('families')
            ->whereIn('closet_type', ['Leher angsa', 'leher angsa'])
            ->update(['closet_type' => 'Leher Angsa']);

        DB::table('families')
            ->whereIn('closet_type', ['Plengsengan dengan tutup', 'plengsengan dengan tutup'])
            ->update(['closet_type' => 'Plengsengan dengan Tutup']);

        DB::table('families')
            ->whereIn('closet_type', ['Cemplung/cubluk', 'cemplung/cubluk', 'Cemplung / cubluk'])
            ->update(['closet_type' => 'Cemplung / Cubluk']);

        // 4. Standarisasi Fasilitas BAB (toilet_facility)
        DB::table('families')
            ->whereIn('toilet_facility', ['Tidak ada', 'tidak ada'])
            ->update(['toilet_facility' => 'Tidak Ada']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op demi integritas data
    }
};
