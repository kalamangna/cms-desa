<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MinifyHtml
{
    /**
     * Pola regex untuk menghapus whitespace berlebih dari HTML output.
     * Mengecualikan konten di dalam <pre>, <textarea>, <script>, dan <style>
     * agar tidak merusak pemformatan kode dan skrip.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Lewati request admin panel, Livewire, atau debug tools untuk mencegah crash / malfungsi state
        if ($request->is('admin*') || $request->is('livewire*') || $request->is('_debugbar*')) {
            return $response;
        }

        // Hanya minifikasi response HTML biasa
        $contentType = $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'text/html')) {
            return $response;
        }

        $content = $response->getContent();
        if (empty($content)) {
            return $response;
        }

        $minified = $this->minify($content);
        
        // Pengaman: Jika proses minifikasi menghasilkan string kosong/null (error regex), 
        // kembalikan konten asli agar halaman tidak blank.
        if (!empty($minified)) {
            $response->setContent($minified);
        }

        return $response;
    }

    protected function minify(string $html): string
    {
        // Simpan blok yang tidak boleh dimodifikasi
        $preserve = [];
        // PENTING: placeholder TIDAK boleh berbentuk komentar HTML (<!-- -->)
        // karena akan ikut terhapus oleh langkah penghapusan komentar di bawah.
        $placeholder = '__MINIFY_BLOCK_%d__';

        // Lindungi <pre>, <textarea>, <script>, <style> dari minifikasi
        $result = preg_replace_callback(
            '/<(pre|textarea|script|style)(\s[^>]*)?>.*?<\/\1>/si',
            function ($matches) use (&$preserve, $placeholder) {
                $key = count($preserve);
                $preserve[$key] = $matches[0];
                return sprintf($placeholder, $key);
            },
            $html
        );

        if ($result === null || preg_last_error() !== PREG_NO_ERROR) {
            return '';
        }
        $html = $result;

        // Hapus komentar HTML (kecuali conditional comments IE dan komentar khusus)
        $result = preg_replace('/<!--(?!\[if\s)(?!-->).*?-->/si', '', $html);
        if ($result === null || preg_last_error() !== PREG_NO_ERROR) {
            return '';
        }
        $html = $result;

        // Hapus whitespace antar tag
        $result = preg_replace('/>\s+</s', '> <', $html);
        if ($result === null || preg_last_error() !== PREG_NO_ERROR) {
            return '';
        }
        $html = $result;

        // Normalisasi whitespace di dalam tag HTML (antar atribut yang ditulis multi-line).
        // Ini penting agar atribut Alpine.js (x-show, x-transition, x-cloak, dll.)
        // yang ditulis di baris terpisah tidak kehilangan spasi pemisah antar atribut.
        $result = preg_replace_callback('/<[^>]+>/s', function ($matches) {
            // Ganti newline dan whitespace berlebih di dalam tag dengan satu spasi
            return preg_replace('/\s+/', ' ', $matches[0]);
        }, $html);
        if ($result === null || preg_last_error() !== PREG_NO_ERROR) {
            return '';
        }
        $html = $result;

        // Trim whitespace di awal dan akhir baris
        $result = preg_replace('/^\s+/m', '', $html);
        if ($result === null || preg_last_error() !== PREG_NO_ERROR) {
            return '';
        }
        $html = $result;

        $result = preg_replace('/\s+$/m', '', $html);
        if ($result === null || preg_last_error() !== PREG_NO_ERROR) {
            return '';
        }
        $html = $result;

        // Hapus baris kosong berganda
        $result = preg_replace('/\n{2,}/', "\n", $html);
        if ($result === null || preg_last_error() !== PREG_NO_ERROR) {
            return '';
        }
        $html = $result;

        // Kembalikan blok yang dilindungi
        foreach ($preserve as $key => $block) {
            $html = str_replace(sprintf($placeholder, $key), $block, $html);
        }

        return trim($html);
    }
}
