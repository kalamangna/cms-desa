<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Standardize house characteristic values in families table and statistic_indicators table (Title Case with clean slash spacing).
     */
    public function up(): void
    {
        // 1. building_type
        $bldMappings = [
            'Rumah tinggal tunggal' => 'Rumah Tinggal Tunggal',
            'rumah tinggal tunggal' => 'Rumah Tinggal Tunggal',
        ];
        foreach ($bldMappings as $old => $new) {
            DB::table('families')->where('building_type', $old)->update(['building_type' => $new]);
            DB::table('statistic_indicators')->where('mapping_column', 'building_type')->where('mapping_value', $old)->update(['mapping_value' => $new, 'name' => $new]);
        }

        // 2. ownership_status
        $ownMappings = [
            'Milik sendiri' => 'Milik Sendiri',
            'milik sendiri' => 'Milik Sendiri',
            'Bebas sewa' => 'Bebas Sewa',
            'Bebas Sewa' => 'Bebas Sewa',
            'bebas sewa' => 'Bebas Sewa',
            'Sewa/Kontrak' => 'Sewa / Kontrak',
            'sewa/kontrak' => 'Sewa / Kontrak',
        ];
        foreach ($ownMappings as $old => $new) {
            DB::table('families')->where('ownership_status', $old)->update(['ownership_status' => $new]);
            DB::table('statistic_indicators')->where('mapping_column', 'ownership_status')->where('mapping_value', $old)->update(['mapping_value' => $new, 'name' => $new]);
        }

        // 3. ownership_proof
        $proofMappings = [
            'Tidak punya' => 'Tidak Punya',
            'tidak punya' => 'Tidak Punya',
            'shm' => 'SHM',
        ];
        foreach ($proofMappings as $old => $new) {
            DB::table('families')->where('ownership_proof', $old)->update(['ownership_proof' => $new]);
            DB::table('statistic_indicators')->where('mapping_column', 'ownership_proof')->where('mapping_value', $old)->update(['mapping_value' => $new, 'name' => $new]);
        }

        // 4. floor_material
        $floorMappings = [
            'Semen/bata merah' => 'Semen / Bata Merah',
            'semen/bata merah' => 'Semen / Bata Merah',
            'Kayu/papan' => 'Kayu / Papan',
            'kayu/papan' => 'Kayu / Papan',
            'Ubin/tegel/teraso' => 'Ubin / Tegel / Teraso',
            'ubin/tegel/teraso' => 'Ubin / Tegel / Teraso',
            'Parket/vinil/karpet' => 'Parket / Vinil / Karpet',
            'parket/vinil/karpet' => 'Parket / Vinil / Karpet',
            'keramik' => 'Keramik',
            'tanah' => 'Tanah',
        ];
        foreach ($floorMappings as $old => $new) {
            DB::table('families')->where('floor_material', $old)->update(['floor_material' => $new]);
            DB::table('statistic_indicators')->where('mapping_column', 'floor_material')->where('mapping_value', $old)->update(['mapping_value' => $new, 'name' => $new]);
        }

        // 5. wall_material
        $wallMappings = [
            'Kayu/papan/gipsum/GRC/calciboard' => 'Kayu / Papan / Gipsum / GRC / Calciboard',
            'kayu/papan/gipsum/grc/calciboard' => 'Kayu / Papan / Gipsum / GRC / Calciboard',
            'tembok' => 'Tembok',
            'seng' => 'Seng',
        ];
        foreach ($wallMappings as $old => $new) {
            DB::table('families')->where('wall_material', $old)->update(['wall_material' => $new]);
            DB::table('statistic_indicators')->where('mapping_column', 'wall_material')->where('mapping_value', $old)->update(['mapping_value' => $new, 'name' => $new]);
        }

        // 6. roof_material
        $roofMappings = [
            'seng' => 'Seng',
            'genteng' => 'Genteng',
            'asbes' => 'Asbes',
        ];
        foreach ($roofMappings as $old => $new) {
            DB::table('families')->where('roof_material', $old)->update(['roof_material' => $new]);
            DB::table('statistic_indicators')->where('mapping_column', 'roof_material')->where('mapping_value', $old)->update(['mapping_value' => $new, 'name' => $new]);
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
