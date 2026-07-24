<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Standardize education_level, education, job, and job_status in citizens table and statistic_indicators table
     * so that graph labels on homepage and statistics page are neat, Title Case, and consistent.
     */
    public function up(): void
    {
        // 1. Standardize education_level & education
        $eduMappings = [
            'Tidak punya ijazah SD' => 'Tidak Punya Ijazah SD',
            'tidak punya ijazah sd' => 'Tidak Punya Ijazah SD',
            'SD/sederajat' => 'SD / Sederajat',
            'sd/sederajat' => 'SD / Sederajat',
            'SMP/sederajat' => 'SMP / Sederajat',
            'smp/sederajat' => 'SMP / Sederajat',
            'SMA/sederajat' => 'SMA / Sederajat',
            'sma/sederajat' => 'SMA / Sederajat',
            'D1/D2/D3' => 'D1 / D2 / D3',
            'd1/d2/d3' => 'D1 / D2 / D3',
            'D4/S1/Profesi' => 'D4 / S1 / Profesi',
            'd4/s1/profesi' => 'D4 / S1 / Profesi',
            'S2/S3' => 'S2 / S3',
            's2/s3' => 'S2 / S3',
        ];

        foreach ($eduMappings as $old => $new) {
            DB::table('citizens')->where('education_level', $old)->update(['education_level' => $new]);
            DB::table('citizens')->where('education', $old)->update(['education' => $new]);
            DB::table('statistic_indicators')->where('mapping_column', 'education_level')->where('mapping_value', $old)->update(['mapping_value' => $new, 'name' => $new]);
            DB::table('statistic_indicators')->where('mapping_column', 'education')->where('mapping_value', $old)->update(['mapping_value' => $new, 'name' => $new]);
        }

        // 2. Standardize job_status
        $jobStatusMappings = [
            'Berusaha sendiri' => 'Berusaha Sendiri',
            'Buruh/karyawan/pegawai swasta' => 'Buruh / Karyawan / Pegawai Swasta',
            'Pekerja bebas' => 'Pekerja Bebas',
            'Pekerja keluarga/tidak dibayar' => 'Pekerja Keluarga / Tidak Dibayar',
            'ASN/TNI/Polri/BUMN/BUMD/pejabat negara' => 'ASN / TNI / Polri / BUMN / BUMD / Pejabat Negara',
            'Berusaha dibantu buruh' => 'Berusaha Dibantu Buruh',
        ];

        foreach ($jobStatusMappings as $old => $new) {
            DB::table('citizens')->where('job_status', $old)->update(['job_status' => $new]);
            DB::table('statistic_indicators')->where('mapping_column', 'job_status')->where('mapping_value', $old)->update(['mapping_value' => $new, 'name' => $new]);
        }

        // 3. Standardize job
        $jobMappings = [
            'Belum / tidak bekerja' => 'Belum / Tidak Bekerja',
            'Ibu rumah tangga' => 'Ibu Rumah Tangga',
            'Petani / pekebun' => 'Petani / Pekebun',
            'Pelajar / mahasiswa' => 'Pelajar / Mahasiswa',
            'Wiraswasta / pengusaha' => 'Wiraswasta / Pengusaha',
            'Karyawan swasta' => 'Karyawan Swasta',
            'Tenaga pendidikan' => 'Tenaga Pendidikan',
            'Tenaga kesehatan' => 'Tenaga Kesehatan',
            'Pns / aparatur' => 'PNS / Aparatur',
            'Buruh / pekerja harian' => 'Buruh / Pekerja Harian',
            'Pekerja jasa & transportasi' => 'Pekerja Jasa & Transportasi',
        ];

        foreach ($jobMappings as $old => $new) {
            DB::table('citizens')->where('job', $old)->update(['job' => $new]);
            DB::table('statistic_indicators')->where('mapping_column', 'job')->where('mapping_value', $old)->update(['mapping_value' => $new, 'name' => $new]);
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
