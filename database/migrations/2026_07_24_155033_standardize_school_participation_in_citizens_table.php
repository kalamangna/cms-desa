<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Standardize school_participation in citizens table to Title Case.
     */
    public function up(): void
    {
        $mappings = [
            'Tidak/belum pernah sekolah' => 'Tidak / Belum Pernah Sekolah',
            'tidak/belum pernah sekolah' => 'Tidak / Belum Pernah Sekolah',
            'Tidak / belum pernah sekolah' => 'Tidak / Belum Pernah Sekolah',
            'Masih sekolah' => 'Masih Sekolah',
            'masih sekolah' => 'Masih Sekolah',
            'Tidak bersekolah lagi' => 'Tidak Bersekolah Lagi',
            'tidak bersekolah lagi' => 'Tidak Bersekolah Lagi',
        ];

        foreach ($mappings as $old => $new) {
            DB::table('citizens')
                ->where('school_participation', $old)
                ->update(['school_participation' => $new]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
