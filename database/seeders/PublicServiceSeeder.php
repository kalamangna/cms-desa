<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Complaint;
use App\Models\GuestBook;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class PublicServiceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Membersihkan dan membuat data Layanan, Permohonan, Pengaduan, dan Buku Tamu...');
        
        Service::query()->forceDelete();
        ServiceRequest::query()->forceDelete();
        Complaint::query()->forceDelete();
        GuestBook::query()->forceDelete();

        $faker = Faker::create('id_ID');

        // 1. Layanan
        $services = [
            ['title' => 'Surat Pengantar KTP', 'description' => 'Layanan pembuatan surat pengantar untuk perekaman e-KTP di kecamatan.', 'requirements' => "<ol><li>Fotokopi KK</li><li>Pengantar RT/RW</li><li>Pas foto 3x4</li></ol>"],
            ['title' => 'Surat Pengantar Kartu Keluarga', 'description' => 'Layanan pembuatan/perubahan KK.', 'requirements' => "<ol><li>KK Asli/Fotokopi</li><li>Buku Nikah</li><li>Pengantar RT/RW</li></ol>"],
            ['title' => 'Surat Keterangan Usaha (SKU)', 'description' => 'Surat keterangan resmi dari desa untuk warga yang memiliki usaha.', 'requirements' => "<ol><li>Fotokopi KTP dan KK</li><li>Foto Tempat Usaha</li><li>Pengantar RT/RW</li></ol>"],
            ['title' => 'Surat Keterangan Tidak Mampu', 'description' => 'Surat keterangan untuk fasilitas keringanan biaya.', 'requirements' => "<ol><li>Fotokopi KTP dan KK</li><li>Pengantar RT/RW</li><li>Foto kondisi rumah</li></ol>"],
            ['title' => 'Surat Keterangan Domisili', 'description' => 'Surat keterangan tinggal/domisili.', 'requirements' => "<ol><li>KTP asal</li><li>Pengantar RT/RW</li></ol>"],
        ];

        foreach ($services as $s) {
            $s['slug'] = Str::slug($s['title']);
            Service::create($s);
        }

        $allServices = Service::all();

        // 2. Permohonan
        if ($allServices->count() > 0) {
            for ($i = 0; $i < 5; $i++) {
                $status = $faker->randomElement(['Menunggu', 'Diproses', 'Selesai', 'Ditolak']);
                $adminResponse = match($status) {
                    'Selesai' => 'Dokumen telah selesai dicetak.',
                    'Ditolak' => 'Berkas kurang lengkap, mohon periksa kembali.',
                    'Diproses' => 'Berkas sedang diverifikasi.',
                    default => null
                };

                ServiceRequest::create([
                    'ticket_number' => strtoupper(Str::random(10)),
                    'nik' => $faker->numerify('3201##########'),
                    'name' => $faker->name,
                    'phone' => '08' . $faker->numerify('##########'),
                    'service_id' => $allServices->random()->id,
                    'status' => $status,
                    'admin_response' => $adminResponse,
                ]);
            }
        }

        // 3. Pengaduan
        $complaints = [
            ['ticket_number' => strtoupper(Str::random(10)), 'name' => 'Budi Santoso', 'phone' => '081234567890', 'title' => 'Jalan Berlubang di Pertigaan', 'content' => 'Mohon perbaikan jalan berlubang di pertigaan pasar.', 'status' => 'Menunggu', 'response' => null],
            ['ticket_number' => strtoupper(Str::random(10)), 'name' => 'Siti Aisyah', 'phone' => '085711223344', 'title' => 'Lampu Jalan Mati', 'content' => 'Lampu penerangan jalan depan balai desa mati total.', 'status' => 'Selesai', 'response' => 'Terima kasih, sudah diperbaiki oleh PLN.'],
        ];

        foreach ($complaints as $c) {
            Complaint::create($c);
        }

        // 4. Buku Tamu
        $guestbooks = [
            ['name' => 'Inspektorat Kabupaten', 'institution_address' => 'Kantor Inspektorat', 'phone' => '081122334455', 'purpose' => 'Kunjungan kerja audit APBDes.'],
            ['name' => 'Mahasiswa KKN', 'institution_address' => 'Universitas', 'phone' => '085611223344', 'purpose' => 'Pembukaan KKN.'],
        ];

        foreach ($guestbooks as $gb) {
            GuestBook::create($gb);
        }

        $this->command->info('Berhasil membuat data layanan (5), permohonan (5), pengaduan (2), dan buku tamu (2).');
    }
}
