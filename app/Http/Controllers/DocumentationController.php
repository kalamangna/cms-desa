<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentationController extends Controller
{
    /**
     * Map of valid document keys to titles and filenames in docs/
     */
    protected array $documents = [
        'full' => [
            'title' => 'Laporan Teknis Master (Lengkap)',
            'file' => 'TECHNICAL_REPORT.md',
            'desc' => 'Kompilasi utuh Cover, Pengesahan, Kata Pengantar, BAB I s/d X, dan Lampiran.',
            'icon' => '📑',
        ],
        'cover' => [
            'title' => 'Cover & Lembar Pengesahan',
            'file' => '0_COVER_AND_PREFACE.md',
            'desc' => 'Cover Resmi, Lembar Pengesahan Kepala Desa & Prakom, Kata Pengantar, Daftar Isi.',
            'icon' => '📜',
        ],
        'bab1' => [
            'title' => 'BAB I: Pendahuluan',
            'file' => '1_INTRODUCTION.md',
            'desc' => 'Latar Belakang, Maksud & Tujuan, serta Ruang Lingkup Sistem.',
            'icon' => '📘',
        ],
        'bab2' => [
            'title' => 'BAB II: Analisis Kebutuhan',
            'file' => '2_REQUIREMENTS_ANALYSIS.md',
            'desc' => 'Kebutuhan Fungsional (FR-01 s/d FR-05) & Non-Fungsional.',
            'icon' => '📋',
        ],
        'bab3' => [
            'title' => 'BAB III: Perancangan Sistem',
            'file' => '3_SYSTEM_DESIGN.md',
            'desc' => 'Arsitektur MVC, Data Flow Diagram (DFD), & Alir Sistem.',
            'icon' => '📐',
        ],
        'bab4' => [
            'title' => 'BAB IV: Implementasi',
            'file' => '4_IMPLEMENTATION.md',
            'desc' => 'Implementasi Stacked Bar Chart 2 Arah & Sticky Column Solid.',
            'icon' => '🛠️',
        ],
        'bab5' => [
            'title' => 'BAB V: Basis Data (Database)',
            'file' => '5_DATABASE.md',
            'desc' => 'Spesifikasi Tabel Utama Citizens, Families, & Statistic Categories.',
            'icon' => '🗄️',
        ],
        'bab6' => [
            'title' => 'BAB VI: API & Integration',
            'file' => '6_API.md',
            'desc' => 'Dokumentasi Endpoint /statistik, Layanan Surat, & Pengaduan.',
            'icon' => '🌐',
        ],
        'bab7' => [
            'title' => 'BAB VII: Panduan Instalasi',
            'file' => '7_INSTALLATION_GUIDE.md',
            'desc' => 'Instruksi Pemasangan Lokal & Deployment Hostinger hPanel/SSH.',
            'icon' => '💻',
        ],
        'bab8' => [
            'title' => 'BAB VIII: Panduan Pengguna',
            'file' => '8_USER_GUIDE.md',
            'desc' => 'Manual Book Operator Filament 5 Tab & Penggunaan Publik.',
            'icon' => '📖',
        ],
        'bab9' => [
            'title' => 'BAB IX: Pengujian (Testing)',
            'file' => '9_TESTING.md',
            'desc' => 'Laporan Automated Testing (40 Test PASS) & Matriks UAT.',
            'icon' => '🧪',
        ],
        'bab10' => [
            'title' => 'BAB X: Pemeliharaan',
            'file' => '10_MAINTENANCE.md',
            'desc' => 'Handling Symlink Hosting, Hardening Keamanan, & Clear Cache.',
            'icon' => '🔧',
        ],
        'lampiran' => [
            'title' => 'Lampiran (Appendix A - D)',
            'file' => '11_APPENDIX.md',
            'desc' => 'Ringkasan Changelog, Tangkapan Layar UI, ERD, & Tree Folder.',
            'icon' => '📎',
        ],
        'changelog' => [
            'title' => 'Riwayat Versi (Changelog)',
            'file' => 'CHANGELOG.md',
            'desc' => 'Catatan lengkap riwayat versi aplikasi v1.0.0 s/d v1.8.2.',
            'icon' => '📜',
        ],
    ];

    /**
     * Index page: List all documentation files with Read & PDF Download links
     */
    public function index()
    {
        return view('documentation.index', [
            'documents' => $this->documents,
        ]);
    }

    /**
     * Show Markdown document rendered in HTML
     */
    public function show(string $key)
    {
        if (!isset($this->documents[$key])) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        $doc = $this->documents[$key];
        $filePath = base_path('docs/' . $doc['file']);

        if (!file_exists($filePath)) {
            abort(404, 'Berkas markdown tidak ditemukan.');
        }

        $markdownText = file_get_contents($filePath);
        $htmlContent = Str::markdown($markdownText);

        return view('documentation.show', [
            'docKey' => $key,
            'doc' => $doc,
            'htmlContent' => $htmlContent,
        ]);
    }

    /**
     * Generate & Download PDF dynamically on-the-fly
     */
    public function downloadPdf(string $key)
    {
        if (!isset($this->documents[$key])) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        $doc = $this->documents[$key];
        $filePath = base_path('docs/' . $doc['file']);

        if (!file_exists($filePath)) {
            abort(404, 'Berkas markdown tidak ditemukan.');
        }

        $markdownText = file_get_contents($filePath);
        $htmlBody = Str::markdown($markdownText);

        $pdfHtml = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>' . htmlspecialchars($doc['title']) . '</title>
            <style>
                @page { margin: 20mm 15mm 20mm 15mm; }
                body { font-family: "DejaVu Sans", sans-serif; font-size: 10pt; line-height: 1.5; color: #1e293b; }
                h1 { font-size: 16pt; color: #0f172a; border-bottom: 2px solid #0284c7; padding-bottom: 5px; margin-top: 25px; page-break-before: always; }
                h1:first-of-type { page-break-before: avoid; }
                h2 { font-size: 13pt; color: #0284c7; margin-top: 20px; border-bottom: 1px solid #cbd5e1; padding-bottom: 3px; }
                h3 { font-size: 11pt; color: #334155; margin-top: 15px; }
                table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 9pt; page-break-inside: avoid; }
                th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
                th { background-color: #f1f5f9; color: #0f172a; font-weight: bold; }
                tr:nth-child(even) { background-color: #f8fafc; }
                code, pre { font-family: "DejaVu Sans Mono", monospace; background-color: #f1f5f9; padding: 2px 4px; border-radius: 3px; font-size: 8.5pt; }
                pre { padding: 8px; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; border: 1px solid #e2e8f0; }
                blockquote { border-left: 3px solid #0284c7; margin: 0; padding-left: 10px; color: #475569; font-style: italic; }
                hr { border: none; border-top: 1px solid #e2e8f0; margin: 20px 0; }
            </style>
        </head>
        <body>
        ' . $htmlBody . '
        </body>
        </html>';

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($pdfHtml);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = Str::slug($doc['title']) . '.pdf';

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
