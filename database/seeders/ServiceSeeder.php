<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\GuestBook;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Membersihkan dan membuat data Layanan Desa...');

        Service::query()->forceDelete();
        ServiceRequest::query()->forceDelete();
        Complaint::query()->forceDelete();
        GuestBook::query()->forceDelete();

        // 1. Layanan
        $services = [
            ['title' => 'Surat Pengantar KTP', 'description' => 'Layanan pembuatan surat pengantar untuk perekaman e-KTP di kecamatan.', 'requirements' => '<ol><li>Fotokopi KK</li><li>Pengantar RT/RW</li><li>Pas foto 3x4</li></ol>'],
            ['title' => 'Surat Pengantar Kartu Keluarga', 'description' => 'Layanan pembuatan/perubahan KK.', 'requirements' => '<ol><li>KK Asli/Fotokopi</li><li>Buku Nikah</li><li>Pengantar RT/RW</li></ol>'],
            ['title' => 'Surat Keterangan Usaha (SKU)', 'description' => 'Surat keterangan resmi dari desa untuk warga yang memiliki usaha.', 'requirements' => '<ol><li>Fotokopi KTP dan KK</li><li>Foto Tempat Usaha</li><li>Pengantar RT/RW</li></ol>'],
            ['title' => 'Surat Keterangan Tidak Mampu', 'description' => 'Surat keterangan untuk fasilitas keringanan biaya.', 'requirements' => '<ol><li>Fotokopi KTP dan KK</li><li>Pengantar RT/RW</li><li>Foto kondisi rumah</li></ol>'],
            ['title' => 'Surat Keterangan Domisili', 'description' => 'Surat keterangan tinggal/domisili.', 'requirements' => '<ol><li>KTP asal</li><li>Pengantar RT/RW</li></ol>'],
        ];

        foreach ($services as $s) {
            $s['slug'] = Str::slug($s['title']);
            Service::create($s);
        }

        // Permohonan layanan, pengaduan, dan buku tamu sengaja tidak di-seed agar tetap dikelola manual/ril desa.

        $this->command->info('Berhasil menyuntikkan data 5 Layanan Desa.');
    }
}
