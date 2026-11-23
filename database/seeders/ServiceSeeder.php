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

        // 1. Layanan beserta daftar berkas persyaratan
        $services = [
            [
                'title' => 'Surat Pengantar KTP',
                'description' => 'Layanan pembuatan surat pengantar untuk perekaman e-KTP di kecamatan.',
                'requirements' => '<ol><li>Fotokopi Kartu Keluarga</li><li>Surat Pengantar RT dan RW</li><li>Pas foto ukuran 3x4</li></ol>',
                'items' => [
                    ['name' => 'Foto Kartu Keluarga', 'description' => 'Foto asli atau fotokopi yang terbaca jelas', 'is_required' => true],
                    ['name' => 'Surat Pengantar RT dan RW', 'description' => 'Ditandatangani Ketua RT setempat', 'is_required' => true],
                    ['name' => 'Pas Foto 3x4', 'description' => 'Latar belakang polos', 'is_required' => false],
                ],
            ],
            [
                'title' => 'Surat Pengantar Kartu Keluarga',
                'description' => 'Layanan pembuatan atau perubahan data Kartu Keluarga.',
                'requirements' => '<ol><li>Kartu Keluarga lama</li><li>Buku Nikah bagi yang sudah menikah</li><li>Surat Pengantar RT dan RW</li></ol>',
                'items' => [
                    ['name' => 'Foto Kartu Keluarga Lama', 'description' => 'Foto seluruh halaman KK yang masih terbaca', 'is_required' => true],
                    ['name' => 'Surat Pengantar RT dan RW', 'description' => 'Ditandatangani Ketua RT setempat', 'is_required' => true],
                    ['name' => 'Foto Buku Nikah', 'description' => 'Diunggah jika ada perubahan status perkawinan', 'is_required' => false],
                ],
            ],
            [
                'title' => 'Surat Keterangan Usaha',
                'description' => 'Surat keterangan resmi dari pemerintah desa untuk keperluan usaha warga.',
                'requirements' => '<ol><li>Fotokopi KTP dan Kartu Keluarga</li><li>Foto tempat usaha</li><li>Surat Pengantar RT dan RW</li></ol>',
                'items' => [
                    ['name' => 'Foto KTP Pemohon', 'description' => 'Foto KTP asli yang terbaca jelas', 'is_required' => true],
                    ['name' => 'Foto Kartu Keluarga', 'description' => 'Foto KK asli atau fotokopi', 'is_required' => true],
                    ['name' => 'Foto Tempat Usaha', 'description' => 'Tampak depan lokasi usaha', 'is_required' => true],
                ],
            ],
            [
                'title' => 'Surat Keterangan Tidak Mampu',
                'description' => 'Surat keterangan untuk fasilitas keringanan biaya pendidikan atau kesehatan.',
                'requirements' => '<ol><li>Fotokopi KTP dan Kartu Keluarga</li><li>Surat Pengantar RT dan RW</li><li>Foto kondisi rumah</li></ol>',
                'items' => [
                    ['name' => 'Foto KTP Pemohon', 'description' => 'Foto KTP asli yang terbaca jelas', 'is_required' => true],
                    ['name' => 'Foto Kartu Keluarga', 'description' => 'Foto KK asli atau fotokopi', 'is_required' => true],
                    ['name' => 'Surat Pengantar RT dan RW', 'description' => 'Ditandatangani Ketua RT setempat', 'is_required' => true],
                    ['name' => 'Foto Kondisi Rumah', 'description' => 'Tampak depan rumah tempat tinggal', 'is_required' => false],
                ],
            ],
            [
                'title' => 'Surat Keterangan Domisili',
                'description' => 'Surat keterangan tempat tinggal resmi di wilayah desa.',
                'requirements' => '<ol><li>KTP asal pemohon</li><li>Surat Pengantar RT dan RW</li></ol>',
                'items' => [
                    ['name' => 'Foto KTP Pemohon', 'description' => 'Foto KTP asli yang terbaca jelas', 'is_required' => true],
                    ['name' => 'Surat Pengantar RT dan RW', 'description' => 'Ditandatangani Ketua RT setempat', 'is_required' => true],
                ],
            ],
        ];

        foreach ($services as $s) {
            $items = $s['items'];
            unset($s['items']);

            $s['slug'] = Str::slug($s['title']);
            $service = Service::create($s);

            foreach ($items as $index => $item) {
                $service->requirementItems()->create([
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'is_required' => $item['is_required'],
                    'sort_order' => $index + 1,
                ]);
            }
        }

        // Permohonan layanan, pengaduan, dan buku tamu sengaja tidak di-seed agar tetap dikelola manual/riil desa.

        $this->command->info('Berhasil menyuntikkan data 5 Layanan Desa beserta berkas persyaratan.');
    }
}
