<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix education_level indicator display names (no delete/insert).
     */
    public function up(): void
    {
        $mappings = [
            'Tidak punya ijazah SD' => ['name' => 'Tidak Punya Ijazah SD', 'new_value' => 'Tidak Punya Ijazah SD'],
            'SD/sederajat' => ['name' => 'SD / Sederajat',        'new_value' => 'SD / Sederajat'],
            'SMP/sederajat' => ['name' => 'SMP / Sederajat',       'new_value' => 'SMP / Sederajat'],
            'SMA/sederajat' => ['name' => 'SMA / Sederajat',       'new_value' => 'SMA / Sederajat'],
            'D1/D2/D3' => ['name' => 'D1 / D2 / D3',          'new_value' => 'D1 / D2 / D3'],
            'D4/S1/Profesi' => ['name' => 'D4 / S1 / Profesi',     'new_value' => 'D4 / S1 / Profesi'],
            'S2/S3' => ['name' => 'S2 / S3',               'new_value' => 'S2 / S3'],
        ];

        foreach ($mappings as $old => $data) {
            DB::table('statistic_indicators')
                ->where('mapping_column', 'education_level')
                ->where('mapping_value', $old)
                ->update(['mapping_value' => $data['new_value'], 'name' => $data['name']]);
        }
    }

    public function down(): void
    {
        //
    }
};
