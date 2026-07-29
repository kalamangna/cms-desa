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
        if (Schema::hasTable('galleries') && Schema::hasColumn('galleries', 'image')) {
            DB::table('galleries')
                ->where('image', 'LIKE', '%gallery_dummy%')
                ->update(['image' => null]);
        }

        // Hapus file fisik jika ada di storage lokal/production
        @unlink(storage_path('app/public/settings/gallery_dummy.jpg'));
        @unlink(storage_path('app/public/settings/gallery_dummy.webp'));
        @unlink(public_path('storage/settings/gallery_dummy.jpg'));
        @unlink(public_path('storage/settings/gallery_dummy.webp'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
