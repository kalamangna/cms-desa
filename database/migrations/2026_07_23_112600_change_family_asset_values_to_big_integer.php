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
            $table->bigInteger('motorcycle_value')->default(0)->change();
            $table->bigInteger('car_value')->default(0)->change();
            $table->bigInteger('other_land_value')->default(0)->change();
            $table->bigInteger('other_building_value')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->integer('motorcycle_value')->default(0)->change();
            $table->integer('car_value')->default(0)->change();
            $table->integer('other_land_value')->default(0)->change();
            $table->integer('other_building_value')->default(0)->change();
        });
    }
};
