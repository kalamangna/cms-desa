<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Backfill existing electricity_power text into 3 separate meter columns.
     */
    public function up(): void
    {
        $families = DB::table('families')
            ->whereNotNull('electricity_power')
            ->where('electricity_power', '!=', '')
            ->get();

        foreach ($families as $f) {
            $parts = array_map('trim', explode(',', $f->electricity_power));
            DB::table('families')
                ->where('id', $f->id)
                ->update([
                    'electricity_power_meter_1' => $parts[0] ?? null,
                    'electricity_power_meter_2' => $parts[1] ?? null,
                    'electricity_power_meter_3' => $parts[2] ?? null,
                ]);
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
