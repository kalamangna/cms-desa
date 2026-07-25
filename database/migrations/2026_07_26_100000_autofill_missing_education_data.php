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
            // Otomatis lengkapi data tingkat pendidikan & partisipasi sekolah yang NULL pada DB eksisting
            DB::table('citizens')
                ->whereNull('education_level')
                ->orWhere('education_level', '')
                ->update(['education_level' => 'Tidak Punya Ijazah SD']);

            DB::table('citizens')
                ->whereNull('school_participation')
                ->orWhere('school_participation', '')
                ->update(['school_participation' => 'Tidak/belum pernah sekolah']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op demi menjaga integritas data
    }
};
