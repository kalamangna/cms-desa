<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membersihkan dan membuat data Dokumen Publik...');

        // Membersihkan data lama dokumen
        Document::query()->forceDelete();

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

            if (! Storage::disk('public')->exists($doc['file'])) {
                Storage::disk('public')->put($doc['file'], $dummyPdfContent);
            }
        }

        // Publikasi Data sengaja tidak di-seed agar tetap dikelola manual/ril desa.

        $this->command->info('Berhasil menyuntikkan 5 Dokumen Publik.');
    }
}
