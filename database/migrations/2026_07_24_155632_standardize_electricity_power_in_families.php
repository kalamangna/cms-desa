<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $mappings = [
            '450 watt' => '450 Watt',
            '900 watt' => '900 Watt',
            '1.300 watt' => '1.300 Watt',
            '2.200 watt' => '2.200 Watt',
            '3.500 watt' => '3.500 Watt',
            '4.400 watt' => '4.400 Watt',
            '5.500 watt' => '5.500 Watt',
            '6.600 watt' => '6.600 Watt',
        ];

        foreach (['electricity_power_meter_1', 'electricity_power_meter_2', 'electricity_power_meter_3'] as $column) {
            foreach ($mappings as $old => $new) {
                DB::table('families')
                    ->where($column, $old)
                    ->update([$column => $new]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
