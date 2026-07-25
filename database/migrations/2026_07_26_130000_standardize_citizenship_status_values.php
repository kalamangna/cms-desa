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
        // Standarisasi penulisan Keberadaan Anggota Keluarga (citizenship_status) agar rapi
        DB::table('citizens')
            ->whereIn('citizenship_status', ['Tinggal di rumah/tempat tinggal ini', 'tinggal di rumah/tempat tinggal ini', 'Tinggal di rumah ini'])
            ->update(['citizenship_status' => 'Tinggal di Rumah Ini']);

        DB::table('citizens')
            ->whereIn('citizenship_status', ['Tidak tinggal bersama keluarga/pindah ke wilayah/daerah lain di Indonesia', 'pindah ke wilayah/daerah lain di indonesia'])
            ->update(['citizenship_status' => 'Pindah ke Daerah Lain (Indonesia)']);

        DB::table('citizens')
            ->whereIn('citizenship_status', ['Tidak tinggal bersama keluarga/pindah ke luar negeri', 'pindah ke luar negeri'])
            ->update(['citizenship_status' => 'Pindah ke Luar Negeri']);

        DB::table('citizens')
            ->whereIn('citizenship_status', ['Sudah pisah KK', 'sudah pisah kk'])
            ->update(['citizenship_status' => 'Sudah Pisah KK']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op demi integritas data
    }
};
