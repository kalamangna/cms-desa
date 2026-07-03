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

        // Hanya minifikasi response HTML biasa
        $contentType = $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'text/html')) {
            return $response;
        }

        $content = $response->getContent();
        if (empty($content)) {
            return $response;
        }

        $content = $this->minify($content);
        $response->setContent($content);

        return $response;
    }

    protected function minify(string $html): string
    {
        // Simpan blok yang tidak boleh dimodifikasi
        $preserve = [];
        $placeholder = '<!--MINIFY_PRESERVE_%d-->';

        // Lindungi <pre>, <textarea>, <script>, <style> dari minifikasi
        $html = preg_replace_callback(
            '/<(pre|textarea|script|style)(\s[^>]*)?>.*?<\/\1>/si',
            function ($matches) use (&$preserve, $placeholder) {
                $key = count($preserve);
                $preserve[$key] = $matches[0];
                return sprintf($placeholder, $key);
            },
            $html
        );

        // Hapus komentar HTML (kecuali conditional comments IE dan komentar khusus)
        $html = preg_replace('/<!--(?!\[if\s)(?!-->).*?-->/si', '', $html);

        // Hapus whitespace antar tag
        $html = preg_replace('/>\s+</s', '> <', $html);

        // Trim whitespace di awal dan akhir baris
        $html = preg_replace('/^\s+/m', '', $html);
        $html = preg_replace('/\s+$/m', '', $html);

        // Hapus baris kosong berganda
        $html = preg_replace('/\n{2,}/', "\n", $html);

        // Kembalikan blok yang dilindungi
        foreach ($preserve as $key => $block) {
            $html = str_replace(sprintf($placeholder, $key), $block, $html);
        }

        return trim($html);
    }
}
