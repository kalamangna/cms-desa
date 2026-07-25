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
        // Standarisasi penulisan Partisipasi Sekolah agar cocok dengan dropdown Filament Form
        DB::table('citizens')
            ->whereIn('school_participation', ['Tidak/belum pernah sekolah', 'tidak/belum pernah sekolah', 'Tidak/Belum Pernah Sekolah'])
            ->update(['school_participation' => 'Tidak / Belum Pernah Sekolah']);

        DB::table('citizens')
            ->whereIn('school_participation', ['Masih sekolah', 'masih sekolah', 'Masih Sekolah'])
            ->update(['school_participation' => 'Masih Sekolah']);

        DB::table('citizens')
            ->whereIn('school_participation', ['Tidak bersekolah lagi', 'tidak bersekolah lagi', 'Tidak Bersekolah Lagi'])
            ->update(['school_participation' => 'Tidak Bersekolah Lagi']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op demi integritas data
    }
};
