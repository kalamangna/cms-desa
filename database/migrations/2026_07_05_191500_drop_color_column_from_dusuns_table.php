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
        Schema::table('dusuns', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dusuns', function (Blueprint $table) {
            $table->string('color', 10)->default('#10b981')->after('geojson');
        });
    }
};
