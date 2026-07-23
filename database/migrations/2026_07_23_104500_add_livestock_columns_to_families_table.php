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
        Schema::table('families', function (Blueprint $table) {
            if (!Schema::hasColumn('families', 'cow_count')) {
                $table->integer('cow_count')->default(0)->after('other_building_value');
            }
            if (!Schema::hasColumn('families', 'goat_count')) {
                $table->integer('goat_count')->default(0)->after('cow_count');
            }
            if (!Schema::hasColumn('families', 'buffalo_count')) {
                $table->integer('buffalo_count')->default(0)->after('goat_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->dropColumn(['cow_count', 'goat_count', 'buffalo_count']);
        });
    }
};
