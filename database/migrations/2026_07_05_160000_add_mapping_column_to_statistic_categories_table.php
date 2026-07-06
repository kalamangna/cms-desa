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
        Schema::table('statistic_categories', function (Blueprint $table) {
            $table->string('mapping_column')->nullable()->after('mapping_table');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statistic_categories', function (Blueprint $table) {
            $table->dropColumn('mapping_column');
        });
    }
};
