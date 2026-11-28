<?php

use App\Services\LetterNumberService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Memetakan kode klasifikasi surat lama bawaan ke standar Permendagri Nomor 83 Tahun 2022
     * tanpa mengubah kode kustom yang telah disesuaikan oleh pemerintah desa.
     */
    public function up(): void
    {
        if (Schema::hasTable('services') && Schema::hasColumn('services', 'letter_code')) {
            $codeMap = [
                ['470', '400.12.2'],
                ['474.4', '400.12.2'],
                ['474.1', '400.12.2'],
                ['475', '400.12.2'],
                ['472.11', '400.12.3'],
                ['472.12', '400.12.3'],
                ['474.2', '400.12.3'],
                ['503', '500.3'],
                ['401', '400.9'],
                ['300', '300.1'],
            ];

            foreach ($codeMap as [$legacyCode, $newCode]) {
                DB::table('services')
                    ->where('letter_code', '=', (string) $legacyCode)
                    ->update(['letter_code' => $newCode]);
            }
        }

        if (Schema::hasTable('settings')) {
            $legacyFormat = '{kode_klasifikasi}/{nomor_urut}/{kode_desa}/{bulan_romawi}/{tahun}';
            DB::table('settings')
                ->where('key', 'tte_letter_number_format')
                ->where('value', $legacyFormat)
                ->update(['value' => LetterNumberService::DEFAULT_FORMAT]);
        }

        Cache::forget('services_list');
        Cache::forget('site_settings_all');
    }

    public function down(): void
    {
        // Tidak membalikkan data kode klasifikasi untuk menjaga konsistensi nomor surat terbit.
    }
};
