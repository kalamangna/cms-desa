<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrasi ini dibiarkan no-op agar tidak menghapus data kustom yang mungkin sengaja dibuat oleh Admin Desa.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
