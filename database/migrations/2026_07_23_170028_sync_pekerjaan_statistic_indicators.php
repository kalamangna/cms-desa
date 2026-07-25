<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Standardize 'pekerjaan' category indicator mapping_value and names (no delete/insert).
     */
    public function up(): void
    {
        $mappings = [
            'Belum / tidak bekerja'        => 'Belum / Tidak Bekerja',
            'Ibu rumah tangga'             => 'Ibu Rumah Tangga',
            'Petani / pekebun'             => 'Petani / Pekebun',
            'Pelajar / mahasiswa'          => 'Pelajar / Mahasiswa',
            'Wiraswasta / pengusaha'       => 'Wiraswasta / Pengusaha',
            'Karyawan swasta'              => 'Karyawan Swasta',
            'Tenaga pendidikan'            => 'Tenaga Pendidikan',
            'Tenaga kesehatan'             => 'Tenaga Kesehatan',
            'Pns / aparatur'               => 'PNS / Aparatur',
            'Buruh / pekerja harian'       => 'Buruh / Pekerja Harian',
            'Pekerja jasa & transportasi'  => 'Pekerja Jasa & Transportasi',
        ];

        foreach ($mappings as $old => $new) {
            DB::table('statistic_indicators')
                ->where('mapping_column', 'job')
                ->where('mapping_value', $old)
                ->update(['mapping_value' => $new, 'name' => $new]);
        }
    }

    public function down(): void
    {
        //
    }
};
