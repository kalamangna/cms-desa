<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // === 1. PEMBERSIHAN TABEL CITIZENS ===
        if (Schema::hasTable('citizens')) {
            // marital_status (Status Perkawinan kosong -> Belum Kawin)
            DB::table('citizens')
                ->whereNull('marital_status')
                ->orWhere('marital_status', '')
                ->update(['marital_status' => 'Belum Kawin']);

            // family_relation (Hubungan Keluarga kosong -> Famili Lain)
            DB::table('citizens')
                ->whereNull('family_relation')
                ->orWhere('family_relation', '')
                ->update(['family_relation' => 'Famili Lain']);

            // job_status (Status Pekerjaan kosong -> Tidak Bekerja / Lainnya)
            DB::table('citizens')
                ->whereNull('job_status')
                ->orWhere('job_status', '')
                ->update(['job_status' => 'Tidak Bekerja / Lainnya']);

            // domicile_address_type (Kecocokan Domisili kosong -> Sesuai KK dan KTP)
            DB::table('citizens')
                ->whereNull('domicile_address_type')
                ->orWhere('domicile_address_type', '')
                ->update(['domicile_address_type' => 'Sesuai KK dan KTP']);
        }

        // === 2. PEMBERSIHAN TABEL FAMILIES ===
        if (Schema::hasTable('families')) {
            // ownership_proof (Bukti Kepemilikan)
            DB::table('families')
                ->whereIn('ownership_proof', ['tidak punya', 'Tidak punya', '', null])
                ->orWhereNull('ownership_proof')
                ->update(['ownership_proof' => 'Tidak Punya']);

            // feces_disposal (Pembuangan Tinja kosong -> Tidak Ada)
            DB::table('families')
                ->whereNull('feces_disposal')
                ->orWhere('feces_disposal', '')
                ->update(['feces_disposal' => 'Tidak Ada']);

            // lighting_source (Sumber Penerangan)
            DB::table('families')
                ->whereIn('lighting_source', ['Listrik PLN dengan meteran', 'listrik pln dengan meteran'])
                ->update(['lighting_source' => 'Listrik PLN Dengan Meteran']);

            DB::table('families')
                ->whereIn('lighting_source', ['Listrik PLN tanpa meteran', 'Listrik Tampa Meteran', 'listrik pln tanpa meteran'])
                ->update(['lighting_source' => 'Listrik PLN Tanpa Meteran']);

            DB::table('families')
                ->whereIn('lighting_source', ['Listrik non-PLN', 'listrik non-pln'])
                ->update(['lighting_source' => 'Listrik Non-PLN']);

            DB::table('families')
                ->whereIn('lighting_source', ['Bukan listrik', 'bukan listrik'])
                ->update(['lighting_source' => 'Bukan Listrik']);

            // electricity_power (Daya Listrik kosong -> Tidak Ada)
            DB::table('families')
                ->whereNull('electricity_power')
                ->orWhere('electricity_power', '')
                ->update(['electricity_power' => 'Tidak Ada']);

            // assistance_type (Bantuan Sosial kosong -> Tidak Ada)
            DB::table('families')
                ->whereNull('assistance_type')
                ->orWhere('assistance_type', '')
                ->update(['assistance_type' => 'Tidak Ada']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op demi integritas data
    }
};
