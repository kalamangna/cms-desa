<?php

namespace App\Console\Commands;

use App\Models\StatisticData;
use Illuminate\Console\Command;

class CleanupOldStatisticData extends Command
{
    protected $signature = 'statistics:cleanup-old {--years=3 : Jumlah tahun data yang ingin dipertahankan}';

    protected $description = 'Hapus data statistik yang lebih lama dari threshold tertentu';

    public function handle(): int
    {
        $years = (int) $this->option('years');

        if ($years < 1) {
            $this->error('Parameter --years harus lebih dari 0.');

            return static::FAILURE;
        }

        $cutoffYear = (int) date('Y') - $years;

        $deletedCount = StatisticData::where('year', '<', $cutoffYear)->delete();

        if ($deletedCount === 0) {
            $this->info("Tidak ada data statistik sebelum tahun {$cutoffYear}.");

            return static::SUCCESS;
        }

        $this->info("Berhasil menghapus {$deletedCount} baris data statistik sebelum tahun {$cutoffYear}.");

        return static::SUCCESS;
    }
}
