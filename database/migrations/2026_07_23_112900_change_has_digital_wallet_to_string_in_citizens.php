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
        // Guard: Pada fresh install, has_digital_wallet sudah dibuat sebagai string nullable di base CREATE migration.
        // Migration ini hanya relevan untuk database existing yang masih menggunakan boolean.
        if (! Schema::hasTable('citizens') || ! Schema::hasColumn('citizens', 'has_digital_wallet')) {
            return;
        }
        Schema::table('citizens', function (Blueprint $table) {
            $table->string('has_digital_wallet')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citizens', function (Blueprint $table) {
            $table->boolean('has_digital_wallet')->default(false)->change();
        });
    }
};
