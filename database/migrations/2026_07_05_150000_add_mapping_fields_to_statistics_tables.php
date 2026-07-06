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
            $table->boolean('is_active')->default(true)->after('description');
            $table->string('mapping_table')->nullable()->after('is_active'); // 'citizens' or 'families'
        });

        Schema::table('statistic_indicators', function (Blueprint $table) {
            $table->string('mapping_column')->nullable()->after('unit');
            $table->string('mapping_operator')->default('=')->after('mapping_column'); // '=', 'LIKE', 'whereNotNull', etc.
            $table->string('mapping_value')->nullable()->after('mapping_operator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statistic_categories', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'mapping_table']);
        });

        Schema::table('statistic_indicators', function (Blueprint $table) {
            $table->dropColumn(['mapping_column', 'mapping_operator', 'mapping_value']);
        });
    }
};
