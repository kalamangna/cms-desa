<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Guard: Pada fresh install, kolom sudah dibuat nullable di base CREATE migration.
        // Migration ini hanya relevan untuk database existing yang dibuat sebelum perubahan base.
        if (Schema::hasTable('galleries') && Schema::hasColumn('galleries', 'image')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->string('image')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('galleries', 'image')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->string('image')->nullable(false)->change();
            });
        }
    }
};
