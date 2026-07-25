<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Standardize 'bantuan-sosial' category indicator mapping_value and names only (no delete/insert).
     * Only updates families data and existing indicators — does NOT create new indicators or categories.
     */
    public function up(): void
    {
        // 1. Clean assistance_type strings in families table
        $families = DB::table('families')->whereNotNull('assistance_type')->get();
        foreach ($families as $f) {
            $raw   = trim($f->assistance_type);
            $lower = strtolower($raw);

            if (empty($raw) || in_array($lower, ['tidak ada', 'tidak', 'none', '-'])) {
                DB::table('families')->where('id', $f->id)->update(['assistance_type' => 'Tidak Ada']);
                continue;
            }

            $items = [];
            if (str_contains($lower, 'pkh'))                                      $items[] = 'PKH';
            if (str_contains($lower, 'bpnt') || str_contains($lower, 'sembako')) $items[] = 'BPNT / Sembako';
            if (str_contains($lower, 'blt'))                                       $items[] = 'BLT Desa';
            if (str_contains($lower, 'listrik') || str_contains($lower, 'subsidi')) $items[] = 'Subsidi Listrik';
            if (str_contains($lower, 'bedah') || str_contains($lower, 'rumah'))   $items[] = 'Bedah Rumah';

            if (empty($items)) {
                $items[] = 'Bantuan Lainnya';
            }

            DB::table('families')->where('id', $f->id)->update([
                'assistance_type' => implode(', ', array_unique($items))
            ]);
        }

        // 2. Standardize existing indicator mapping_values for bantuan-sosial (UPDATE only — no delete/insert)
        $indicatorMappings = [
            ['old_value' => '%PKH%',             'new_value' => '%PKH%',             'new_name' => 'PKH'],
            ['old_value' => '%BPNT%',            'new_value' => '%BPNT%',            'new_name' => 'BPNT / Sembako'],
            ['old_value' => '%BLT%',             'new_value' => '%BLT%',             'new_name' => 'BLT Desa'],
            ['old_value' => '%Subsidi Listrik%', 'new_value' => '%Subsidi Listrik%', 'new_name' => 'Subsidi Listrik'],
            ['old_value' => '%Bedah Rumah%',     'new_value' => '%Bedah Rumah%',     'new_name' => 'Bedah Rumah'],
            ['old_value' => '%Bantuan Lainnya%', 'new_value' => '%Bantuan Lainnya%', 'new_name' => 'Bantuan Lainnya'],
            ['old_value' => 'Tidak Ada',         'new_value' => 'Tidak Ada',         'new_name' => 'Tidak Menerima Bantuan'],
        ];

        foreach ($indicatorMappings as $map) {
            DB::table('statistic_indicators')
                ->where('mapping_column', 'assistance_type')
                ->where('mapping_value', $map['old_value'])
                ->update(['mapping_value' => $map['new_value'], 'name' => $map['new_name']]);
        }
    }

    public function down(): void
    {
        // No-op
    }
};
