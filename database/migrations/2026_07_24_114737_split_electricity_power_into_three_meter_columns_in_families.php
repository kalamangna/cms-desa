<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Split electricity_power into 3 separate meter columns:
     * - electricity_power_meter_1
     * - electricity_power_meter_2
     * - electricity_power_meter_3
     */
    public function up(): void
    {
        Schema::table('families', function (Blueprint $table) {
            if (!Schema::hasColumn('families', 'electricity_power_meter_1')) {
                $table->string('electricity_power_meter_1')->nullable()->after('lighting_source');
            }
            if (!Schema::hasColumn('families', 'electricity_power_meter_2')) {
                $table->string('electricity_power_meter_2')->nullable()->after('electricity_power_meter_1');
            }
            if (!Schema::hasColumn('families', 'electricity_power_meter_3')) {
                $table->string('electricity_power_meter_3')->nullable()->after('electricity_power_meter_2');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->dropColumn(['electricity_power_meter_1', 'electricity_power_meter_2', 'electricity_power_meter_3']);
        });
    }
};
