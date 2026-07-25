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
        if (Schema::hasTable('datasets')) {
            Schema::table('datasets', function (Blueprint $table) {
                if (! Schema::hasColumn('datasets', 'source_table')) {
                    $table->string('source_table')->nullable()->after('source');
                }
                if (! Schema::hasColumn('datasets', 'selected_columns')) {
                    $table->json('selected_columns')->nullable()->after('source_table');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('datasets')) {
            Schema::table('datasets', function (Blueprint $table) {
                if (Schema::hasColumn('datasets', 'selected_columns')) {
                    $table->dropColumn('selected_columns');
                }
                if (Schema::hasColumn('datasets', 'source_table')) {
                    $table->dropColumn('source_table');
                }
            });
        }
    }
};
