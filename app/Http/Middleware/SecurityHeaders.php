<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('livewire/preview-file/*')) {
            \Illuminate\Support\Facades\Log::info('LIVEWIRE_PREVIEW_INCOMING', [
                'url' => $request->url(),
                'fullUrl' => $request->fullUrl(),
                'queryString' => $request->server->get('QUERY_STRING'),
                'hasValidSignature' => $request->hasValidSignature(),
                'scheme' => $request->getScheme(),
                'port' => $request->getPort(),
                'host' => $request->getHost(),
                'headers' => $request->headers->all(),
            ]);
        }

        $response = $next($request);

        // Pastikan berkas pratinjau Livewire dikirim sebagai inline agar browser merender tag <img>
        if ($request->is('livewire/preview-file/*')) {
            if ($response->headers->has('Content-Disposition')) {
                $disposition = str_replace('attachment', 'inline', (string) $response->headers->get('Content-Disposition'));
                $response->headers->set('Content-Disposition', $disposition);
            }

            return $response;
        }

        // Jangan terapkan CSP ketat pada Filament Admin Panel agar tidak mengganggu Livewire/Alpine bawaan Filament
        if ($request->is('admin*') || $request->is('livewire*')) {
            return $response;
        }

        // Terapkan Content Security Policy (CSP) modern yang memperbolehkan Vite Dev Server & 'unsafe-eval' untuk Alpine.js
        $csp = "default-src 'self'; ".
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://*:5173 https:; ".
               "worker-src 'self' blob:; ".
               "style-src 'self' 'unsafe-inline' http://*:5173 https:; ".
               "font-src 'self' data: https:; ".
               "img-src 'self' data: blob: https:; ".
               "frame-src 'self' https:; ".
               "connect-src 'self' https: http: ws://*:5173 wss://*:5173; ".
               "object-src 'none'; ".
               "base-uri 'self';";

        $response->headers->set('Content-Security-Policy', $csp, true);
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
