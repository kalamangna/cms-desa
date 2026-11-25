<?php

namespace App\Services;

use App\Models\LetterSequence;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LetterNumberService
{
    public const DEFAULT_FORMAT = '{kode_klasifikasi}/{nomor_urut}/{kode_desa}/{bulan_romawi}/{tahun}';

    /**
     * Menghasilkan nomor surat berikutnya secara atomik per tahun.
     */
    public function nextNumber(Service $service, ?Carbon $date = null): string
    {
        $targetDate = $date ?? now();
        $year = (int) $targetDate->year;

        $nextSequence = DB::transaction(function () use ($year): int {
            $sequence = LetterSequence::where('year', $year)->lockForUpdate()->first();

            if (! $sequence) {
                $sequence = LetterSequence::create([
                    'year' => $year,
                    'last_number' => 1,
                ]);

                return 1;
            }

            $sequence->increment('last_number');

            return (int) $sequence->fresh()->last_number;
        });

        return $this->formatNumber($service, $nextSequence, $targetDate);
    }

    /**
     * Pratinjau nomor surat berikutnya tanpa menaikkan counter.
     */
    public function previewNextNumber(Service $service, ?Carbon $date = null): string
    {
        $targetDate = $date ?? now();
        $year = (int) $targetDate->year;
        $lastNumber = (int) (LetterSequence::where('year', $year)->value('last_number') ?? 0);

        return $this->formatNumber($service, $lastNumber + 1, $targetDate);
    }

    private function formatNumber(Service $service, int $sequenceNumber, Carbon $date): string
    {
        $format = Setting::where('key', 'tte_letter_number_format')->value('value') ?: self::DEFAULT_FORMAT;
        $villageCode = Setting::where('key', 'tte_village_code')->value('value') ?: 'DESA';
        $classificationCode = $service->letter_code ?: '470';

        $replacements = [
            '{kode_klasifikasi}' => trim($classificationCode),
            '{nomor_urut}' => str_pad((string) $sequenceNumber, 3, '0', STR_PAD_LEFT),
            '{kode_desa}' => trim($villageCode),
            '{bulan_romawi}' => $this->romanMonth((int) $date->month),
            '{tahun}' => (string) $date->year,
        ];

        return strtr($format, $replacements);
    }

    private function romanMonth(int $month): string
    {
        return match ($month) {
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
            default => 'I',
        };
    }
}
