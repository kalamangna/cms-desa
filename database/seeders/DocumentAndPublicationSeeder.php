<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\Publication;
use Illuminate\Support\Str;

class DocumentAndPublicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membersihkan dan membuat data Dokumen dan Publikasi...');
        
        // Membersihkan data lama
        Document::query()->forceDelete();
        Publication::query()->forceDelete();

        // 1. Dokumen
        $documents = [
            [
                'title' => 'Peraturan Desa (Perdes) No. 1 Tahun 2024 tentang RPJMDes',
                'description' => 'Dokumen resmi Rencana Pembangunan Jangka Menengah Desa (RPJMDes) untuk periode 2024-2030.',
                'file' => 'documents/perdes-no1-2024-rpjmdes.pdf',
            ],
            [
                'title' => 'Formulir Pendaftaran BUMDes',
                'description' => 'Formulir kosong bagi warga yang ingin mendaftarkan unit usahanya sebagai mitra BUMDes.',
                'file' => 'documents/formulir-pendaftaran-bumdes.pdf',
            ],
            [
                'title' => 'SK Kepala Desa tentang Satgas Kebersihan',
                'description' => 'Surat Keputusan pengangkatan Satuan Tugas Kebersihan tingkat RT/RW di lingkungan desa.',
                'file' => 'documents/sk-satgas-kebersihan.pdf',
            ],
            [
                'title' => 'Format Surat Kuasa Ahli Waris',
                'description' => 'Contoh format standar pembuatan surat kuasa ahli waris yang diakui oleh pemerintah desa.',
                'file' => 'documents/format-surat-kuasa-ahli-waris.pdf',
            ],
            [
                'title' => 'Laporan Pertanggungjawaban (LPJ) Dana Desa 2023',
                'description' => 'Dokumen rinci alokasi dan realisasi penggunaan Dana Desa tahun anggaran 2023.',
                'file' => 'documents/lpj-dana-desa-2023.pdf',
            ],
        ];

        foreach ($documents as $doc) {
            $doc['slug'] = Str::slug($doc['title']);
            Document::create($doc);
        }

        // 2. Publikasi
        // Asumsi tipe publikasi: APBDes, RPJMDes, LPPD, LKPJ, dll.
        $publications = [
            [
                'title' => 'Infografis APBDes Tahun Anggaran 2024',
                'type' => 'APBDes',
                'year' => 2024,
                'cover' => 'publications/cover-apbdes-2024.png',
                'pdf_file' => 'publications/infografis-apbdes-2024.pdf',
            ],
            [
                'title' => 'Laporan Penyelenggaraan Pemerintahan Desa (LPPD) 2023',
                'type' => 'LPPD',
                'year' => 2023,
                'cover' => 'publications/cover-lppd-2023.png',
                'pdf_file' => 'publications/lppd-2023.pdf',
            ],
            [
                'title' => 'Rencana Kerja Pemerintah Desa (RKPDes) 2024',
                'type' => 'RKPDes',
                'year' => 2024,
                'cover' => 'publications/cover-rkpdes-2024.png',
                'pdf_file' => 'publications/rkpdes-2024.pdf',
            ],
            [
                'title' => 'Buku Profil Desa Tahun 2022',
                'type' => 'Profil',
                'year' => 2022,
                'cover' => 'publications/cover-profil-desa-2022.png',
                'pdf_file' => 'publications/buku-profil-desa-2022.pdf',
            ],
        ];

        foreach ($publications as $pub) {
            $pub['slug'] = Str::slug($pub['title']);
            Publication::create($pub);
        }

        $this->command->info('Berhasil menyuntikkan 5 Dokumen Publik dan 4 Publikasi.');
    }
}
