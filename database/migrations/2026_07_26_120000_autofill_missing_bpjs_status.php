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
        if (Schema::hasTable('citizens')) {
            // Otomatis lengkapi data status BPJS yang NULL pada DB eksisting
            DB::table('citizens')
                ->whereNull('bpjs_status')
                ->orWhere('bpjs_status', '')
                ->update(['bpjs_status' => 'Tidak Terdaftar']);
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
