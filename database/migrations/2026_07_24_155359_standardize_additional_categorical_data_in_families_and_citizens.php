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
        // ==========================================
        // 1. Standardize Citizens Table
        // ==========================================
        $citizenMappings = [
            'marital_status' => [
                'Belum kawin' => 'Belum Kawin',
                'Cerai mati' => 'Cerai Mati',
                'Cerai hidup' => 'Cerai Hidup',
            ],
            'family_relation' => [
                'Orang tua' => 'Orang Tua',
                'Famili Lain' => 'Famili Lain', // Just in case, Title Case is standard
            ],
            'citizenship_status' => [
                'Tidak tinggal bersama keluarga/pindah ke wilayah/daerah lain di Indonesia' => 'Pindah',
                'Tidak tinggal bersama keluarga/pindah ke luar negeri' => 'Pindah',
                'Sudah pisah KK' => 'Tidak tinggal di rumah ini',
            ]
        ];

        foreach ($citizenMappings as $column => $mappings) {
            foreach ($mappings as $old => $new) {
                DB::table('citizens')->where($column, $old)->update([$column => $new]);
            }
        }

        // ==========================================
        // 2. Standardize Families Table
        // ==========================================
        $familyMappings = [
            'toilet_facility' => [
                'Tidak ada' => 'Tidak Ada',
            ],
            'feces_disposal' => [
                'Tangki septik' => 'Tangki Septik',
                '' => null, // empty string to null
            ],
            'water_source' => [
                'sumur terlindung' => 'Sumur Terlindung',
                'Sumur terlindung' => 'Sumur Terlindung',
                'Sumur bor/pompa' => 'Sumur Bor / Pompa',
            ],
            'lighting_source' => [
                'Listrik Tampa Meteran' => 'Listrik PLN Tanpa Meteran',
                'Listrik PLN tanpa meteran' => 'Listrik PLN Tanpa Meteran',
                'Listrik PLN dengan meteran' => 'Listrik PLN Dengan Meteran',
                'Listrik non-PLN' => 'Listrik Non-PLN',
                'Bukan listrik' => 'Bukan Listrik',
            ]
        ];

        foreach ($familyMappings as $column => $mappings) {
            foreach ($mappings as $old => $new) {
                DB::table('families')->where($column, $old)->update([$column => $new]);
            }
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
