<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\GuestBook;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Services\ServiceStandardizationService;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Membersihkan dan membuat data Layanan Desa standar...');

        Service::query()->forceDelete();
        ServiceRequest::query()->forceDelete();
        Complaint::query()->forceDelete();
        GuestBook::query()->forceDelete();

        $result = app(ServiceStandardizationService::class)->standardize();

        // Permohonan layanan, pengaduan, dan buku tamu sengaja tidak di-seed agar tetap dikelola manual oleh desa.
        $this->command->info(sprintf(
            'Berhasil menyuntikkan %d Layanan Desa standar beserta template TTE dan berkas persyaratan.',
            $result['created'],
        ));
    }
}
