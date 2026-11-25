<?php

namespace App\Services;

use App\Exceptions\BsreException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class BsreService
{
    /**
     * Kirim berkas PDF ke API BSrE e-Sign v1 untuk ditandatangani secara elektronik.
     *
     * @return array{pdf_binary: string, is_mock: bool}
     */
    public function signPdf(
        string $absolutePdfPath,
        string $nik,
        string $passphrase,
        string $verificationUrl
    ): array {
        $cleanNik = preg_replace('/\D/', '', $nik);
        if (strlen($cleanNik) !== 16) {
            throw new BsreException('NIK penanda tangan harus tepat 16 digit.');
        }

        if (trim($passphrase) === '') {
            throw new BsreException('Passphrase TTE wajib diisi.');
        }

        if (! is_file($absolutePdfPath) || ! is_readable($absolutePdfPath)) {
            throw new BsreException('Berkas draf PDF tidak ditemukan.');
        }

        $pdfBinary = file_get_contents($absolutePdfPath);
        if ($pdfBinary === false || ! str_starts_with($pdfBinary, '%PDF')) {
            throw new BsreException('Format berkas draf PDF tidak valid.');
        }

        if (config('bsre.mock')) {
            if (trim($passphrase) === 'invalid') {
                throw new BsreException('Simulasi BSrE: Passphrase tidak sesuai.');
            }

            return [
                'pdf_binary' => $pdfBinary,
                'is_mock' => true,
            ];
        }

        $baseUrl = rtrim((string) config('bsre.base_url'), '/');
        $username = (string) config('bsre.username');
        $password = (string) config('bsre.password');

        if ($baseUrl === '' || $username === '' || $password === '') {
            throw new BsreException('Konfigurasi API BSrE belum lengkap di berkas lingkungan.');
        }

        $endpoint = $baseUrl.'/api/sign/pdf';
        $timeout = max(10, (int) config('bsre.timeout', 60));
        $verifySsl = (bool) config('bsre.verify_ssl', true);
        $pageCount = max(1, (int) preg_match_all('/\/Type\s*\/Page\b/', $pdfBinary));

        try {
            $response = Http::withBasicAuth($username, $password)
                ->timeout($timeout)
                ->withOptions(['verify' => $verifySsl])
                ->attach('file', $pdfBinary, basename($absolutePdfPath), ['Content-Type' => 'application/pdf'])
                ->post($endpoint, [
                    'nik' => $cleanNik,
                    'passphrase' => $passphrase,
                    'tampilan' => 'visible',
                    'image' => 'false',
                    'page' => (string) $pageCount,
                    'xAxis' => (string) config('bsre.x_axis', 350),
                    'yAxis' => (string) config('bsre.y_axis', 180),
                    'width' => (string) config('bsre.width', 85),
                    'height' => (string) config('bsre.height', 85),
                    'linkQR' => $verificationUrl,
                ]);
        } catch (Throwable $e) {
            Log::warning('Koneksi ke server BSrE gagal.', [
                'message' => $e->getMessage(),
            ]);

            throw new BsreException('Gagal terhubung ke server BSrE. Periksa koneksi jaringan.');
        }

        $body = $response->body();

        if (! $response->successful() || ! str_starts_with($body, '%PDF')) {
            $errorDetail = $this->extractSafeErrorMessage($body, $response->status());

            Log::warning('Permintaan tanda tangan BSrE ditolak.', [
                'status' => $response->status(),
                'detail' => $errorDetail,
            ]);

            throw new BsreException($errorDetail);
        }

        return [
            'pdf_binary' => $body,
            'is_mock' => false,
        ];
    }

    /**
     * Periksa status sertifikat penanda tangan pada API BSrE.
     *
     * @return array{registered: bool, status: string, message: string}
     */
    public function checkUserStatus(string $nik): array
    {
        $cleanNik = preg_replace('/\D/', '', $nik);
        if (strlen($cleanNik) !== 16) {
            throw new BsreException('NIK harus tepat 16 digit.');
        }

        if (config('bsre.mock')) {
            return [
                'registered' => true,
                'status' => 'ACTIVE',
                'message' => 'Simulasi BSrE: Sertifikat elektronik aktif.',
            ];
        }

        $baseUrl = rtrim((string) config('bsre.base_url'), '/');
        $username = (string) config('bsre.username');
        $password = (string) config('bsre.password');

        if ($baseUrl === '' || $username === '' || $password === '') {
            throw new BsreException('Konfigurasi API BSrE belum lengkap.');
        }

        try {
            $response = Http::withBasicAuth($username, $password)
                ->timeout(max(10, (int) config('bsre.timeout', 60)))
                ->withOptions(['verify' => (bool) config('bsre.verify_ssl', true)])
                ->get($baseUrl.'/api/user/status/'.$cleanNik);
        } catch (Throwable $e) {
            throw new BsreException('Gagal memeriksa status sertifikat ke server BSrE.');
        }

        $json = $response->json();
        if (! $response->successful() || ! is_array($json)) {
            throw new BsreException($this->extractSafeErrorMessage($response->body(), $response->status()));
        }

        $status = strtoupper((string) ($json['status'] ?? $json['data']['status'] ?? 'UNKNOWN'));

        return [
            'registered' => in_array($status, ['ACTIVE', 'ISSUE', 'VALID'], true),
            'status' => $status,
            'message' => (string) ($json['message'] ?? 'Status sertifikat diterima.'),
        ];
    }

    private function extractSafeErrorMessage(string $body, int $status): string
    {
        $decoded = json_decode($body, true);
        if (is_array($decoded)) {
            $message = $decoded['error'] ?? $decoded['message'] ?? null;
            if (is_string($message) && trim($message) !== '') {
                return 'Penandatanganan BSrE gagal: '.trim($message);
            }
        }

        $plainText = trim(strip_tags($body));
        if ($plainText !== '' && ! str_starts_with($plainText, '<')) {
            return 'Penandatanganan BSrE gagal: '.mb_substr($plainText, 0, 200);
        }

        if ($status === 401 || $status === 403) {
            return 'Autentikasi server BSrE ditolak. Periksa kredensial aplikasi.';
        }

        return 'Penandatanganan BSrE gagal diproses dengan kode '.$status.'.';
    }
}
