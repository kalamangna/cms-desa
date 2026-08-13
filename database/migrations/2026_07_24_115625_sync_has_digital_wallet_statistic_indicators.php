<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Standardize 'dompet-digital-rekening' indicator names (no insert if already has indicators).
     * Only updates existing indicators — does NOT create new ones.
     */
    public function up(): void
    {
        $mappings = [
            'Tidak ada' => 'Tidak Ada',
            'Ya untuk pribadi' => 'Ya untuk Pribadi',
            'Ya untuk usaha dan pribadi' => 'Ya untuk Usaha & Pribadi',
            'Ya untuk usaha' => 'Ya untuk Usaha',
        ];

        foreach ($mappings as $old => $new) {
            DB::table('statistic_indicators')
                ->where('mapping_column', 'has_digital_wallet')
                ->where('mapping_value', $old)
                ->update(['mapping_value' => $new, 'name' => $new]);
        }
    }

    public function down(): void
    {
        // No-op
    }
};
