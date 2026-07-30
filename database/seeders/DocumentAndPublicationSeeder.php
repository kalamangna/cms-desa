<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\Publication;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

        $dummyPdfContent = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n5 0 obj\n<< /Length 44 >>\nstream\nBT /F1 24 Tf 100 700 Td (Dokumen Dummy) Tj ET\nendstream\nendobj\nxref\n0 6\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000223 00000 n \n0000000311 00000 n \ntrailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n405\n%%EOF";

        foreach ($documents as $doc) {
            $doc['slug'] = Str::slug($doc['title']);
            Document::create($doc);
            
            if (!Storage::disk('public')->exists($doc['file'])) {
                Storage::disk('public')->put($doc['file'], $dummyPdfContent);
            }
        }

        // Pastikan direktori publications ada
        if (!Storage::disk('public')->exists('publications')) {
            Storage::disk('public')->makeDirectory('publications');
        }
        // Salin meta.webp sebagai dummy cover
        if (!Storage::disk('public')->exists('publications/meta.webp') && file_exists(public_path('img/meta.webp'))) {
            Storage::disk('public')->put('publications/meta.webp', file_get_contents(public_path('img/meta.webp')));
        }

        // 2. Publikasi
        // Asumsi tipe publikasi: APBDes, RPJMDes, LPPD, LKPJ, dll.
        $publications = [
            [
                'title' => 'Infografis APBDes Tahun Anggaran 2024',
                'type' => 'APBDes',
                'year' => 2024,
                'cover' => 'publications/meta.webp',
                'pdf_file' => 'publications/infografis-apbdes-2024.pdf',
            ],
            [
                'title' => 'Laporan Penyelenggaraan Pemerintahan Desa 2023',
                'type' => 'LPPD',
                'year' => 2023,
                'cover' => 'publications/meta.webp',
                'pdf_file' => 'publications/lppd-2023.pdf',
            ],
            [
                'title' => 'RKPDes Tahun 2024',
                'type' => 'RKPDes',
                'year' => 2024,
                'cover' => 'publications/meta.webp',
                'pdf_file' => 'publications/rkpdes-2024.pdf',
            ],
            [
                'title' => 'Buku Profil Desa Tompobulu',
                'type' => 'Profil',
                'year' => 2022,
                'cover' => 'publications/meta.webp',
                'pdf_file' => 'publications/profil-desa-2022.pdf',
            ],
        ];

        foreach ($publications as $pub) {
            $pub['slug'] = Str::slug($pub['title']);
            Publication::create($pub);
            
            if (!Storage::disk('public')->exists($pub['pdf_file'])) {
                Storage::disk('public')->put($pub['pdf_file'], $dummyPdfContent);
            }
        }

        $this->command->info('Berhasil menyuntikkan 5 Dokumen Publik dan 4 Publikasi.');
    }
}
