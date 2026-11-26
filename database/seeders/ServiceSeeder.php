<?php

namespace Database\Seeders;

use App\Services\ServiceStandardizationService;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Menyiapkan 10 Layanan Desa standar...');

        $result = app(ServiceStandardizationService::class)->standardize();

        $this->command->info(sprintf(
            'Standarisasi layanan selesai. Diperbarui: %d, Ditambahkan: %d.',
            $result['updated'],
            $result['created'],
        ));
    }
}
