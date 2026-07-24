<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Clean and normalize all raw job_status variations in citizens table into standard Title Case categories.
     */
    public function up(): void
    {
        $rows = DB::table('citizens')->whereNotNull('job_status')->where('job_status', '!=', '')->get();

        foreach ($rows as $r) {
            $raw = trim($r->job_status);
            $lower = strtolower($raw);
            $clean = null;

            if (str_contains($lower, 'asn') || str_contains($lower, 'tni') || str_contains($lower, 'polri') || str_contains($lower, 'bumn') || str_contains($lower, 'bumd') || str_contains($lower, 'pejabat') || str_contains($lower, 'kades') || str_contains($lower, 'dusun') || str_contains($lower, 'sekretaris') || str_contains($lower, 'bpd') || str_contains($lower, 'kasi') || str_contains($lower, 'pelayanan') || str_contains($lower, 'pemerintahan')) {
                $clean = 'ASN / TNI / Polri / BUMN / BUMD / Pejabat Negara';
            } elseif (str_contains($lower, 'buruh') || str_contains($lower, 'karyawan') || str_contains($lower, 'karywan') || str_contains($lower, 'swasta') || str_contains($lower, 'pembantu')) {
                $clean = 'Buruh / Karyawan / Pegawai Swasta';
            } elseif (str_contains($lower, 'keluarga') || str_contains($lower, 'keluaga') || str_contains($lower, 'tidak dibayar') || str_contains($lower, 'tak dibayar')) {
                $clean = 'Pekerja Keluarga / Tidak Dibayar';
            } elseif (str_contains($lower, 'bebas') || str_contains($lower, 'babas')) {
                $clean = 'Pekerja Bebas';
            } elseif (str_contains($lower, 'dibantu buruh')) {
                $clean = 'Berusaha Dibantu Buruh';
            } elseif (str_contains($lower, 'sendiri') || str_contains($lower, 'sendri') || str_contains($lower, 'sediri') || str_contains($lower, 'pedagang') || str_contains($lower, 'berusaha')) {
                $clean = 'Berusaha Sendiri';
            } elseif (str_contains($lower, 'tidak tahu') || str_contains($lower, 'tak tahu')) {
                $clean = null; // Reset ke null agar tidak mengotori grafik
            } elseif ($raw === 'Lainnya') {
                $clean = 'Lainnya';
            }

            if ($clean !== null && $clean !== $raw) {
                DB::table('citizens')->where('id', $r->id)->update(['job_status' => $clean]);
            } elseif ($clean === null && !empty($raw)) {
                DB::table('citizens')->where('id', $r->id)->update(['job_status' => null]);
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
