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
        if (Schema::hasTable('families')) {
            // Jika nilai dari Excel kosong, berarti 'Tidak Ada'
            DB::table('families')
                ->whereNull('closet_type')
                ->orWhere('closet_type', '')
                ->update(['closet_type' => 'Tidak Ada']);
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
