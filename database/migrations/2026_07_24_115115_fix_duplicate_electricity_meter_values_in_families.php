<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix duplicate meter values in families table caused by legacy merged string backfill.
     */
    public function up(): void
    {
        $families = DB::table('families')
            ->whereNotNull('electricity_power_meter_1')
            ->where('electricity_power_meter_1', '!=', '')
            ->get();

        foreach ($families as $f) {
            if (
                $f->electricity_power_meter_1 &&
                $f->electricity_power_meter_1 === $f->electricity_power_meter_2 &&
                $f->electricity_power_meter_1 === $f->electricity_power_meter_3
            ) {
                DB::table('families')
                    ->where('id', $f->id)
                    ->update([
                        'electricity_power_meter_2' => null,
                        'electricity_power_meter_3' => null,
                        'electricity_power' => $f->electricity_power_meter_1,
                    ]);
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
