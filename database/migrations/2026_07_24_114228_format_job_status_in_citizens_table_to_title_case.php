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
            'Berusaha sendiri' => 'Berusaha Sendiri',
            'Buruh/karyawan/pegawai swasta' => 'Buruh / Karyawan / Pegawai Swasta',
            'Pekerja bebas' => 'Pekerja Bebas',
            'Pekerja keluarga/tidak dibayar' => 'Pekerja Keluarga / Tidak Dibayar',
            'ASN/TNI/Polri/BUMN/BUMD/pejabat negara' => 'ASN / TNI / Polri / BUMN / BUMD / Pejabat Negara',
            'Berusaha dibantu buruh' => 'Berusaha Dibantu Buruh',
        ];

        foreach ($mappings as $old => $new) {
            DB::table('citizens')
                ->where('job_status', $old)
                ->update(['job_status' => $new]);

            DB::table('statistic_indicators')
                ->where('mapping_column', 'job_status')
                ->where('mapping_value', $old)
                ->update(['mapping_value' => $new, 'name' => $new]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $mappings = [
            'Berusaha Sendiri' => 'Berusaha sendiri',
            'Buruh / Karyawan / Pegawai Swasta' => 'Buruh/karyawan/pegawai swasta',
            'Pekerja Bebas' => 'Pekerja bebas',
            'Pekerja Keluarga / Tidak Dibayar' => 'Pekerja keluarga/tidak dibayar',
            'ASN / TNI / Polri / BUMN / BUMD / Pejabat Negara' => 'ASN/TNI/Polri/BUMN/BUMD/pejabat negara',
            'Berusaha Dibantu Buruh' => 'Berusaha dibantu buruh',
        ];

        foreach ($mappings as $new => $old) {
            DB::table('citizens')
                ->where('job_status', $new)
                ->update(['job_status' => $old]);

            DB::table('statistic_indicators')
                ->where('mapping_column', 'job_status')
                ->where('mapping_value', $new)
                ->update(['mapping_value' => $old, 'name' => $old]);
        }
    }
};
