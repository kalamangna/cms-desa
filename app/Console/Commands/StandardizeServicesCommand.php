<?php

namespace App\Console\Commands;

use App\Services\ServiceStandardizationService;
use Illuminate\Console\Command;

class StandardizeServicesCommand extends Command
{
    protected $signature = 'app:standardize-services';

    protected $description = 'Menyeragamkan 10 layanan standar inti desa beserta template surat TTE dan berkas persyaratan';

    public function handle(ServiceStandardizationService $service): int
    {
        $result = $service->standardize();

        $this->info(sprintf(
            'Standarisasi layanan selesai. Diperbarui: %d, Ditambahkan: %d, Layanan lokal dipertahankan: %d.',
            $result['updated'],
            $result['created'],
            $result['preserved_custom'],
        ));

        return self::SUCCESS;
    }
}
