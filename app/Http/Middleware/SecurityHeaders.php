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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Jangan terapkan CSP ketat pada Filament Admin Panel agar tidak mengganggu Livewire/Alpine bawaan Filament
        if ($request->is('admin*') || $request->is('livewire*')) {
            return $response;
        }

        // Terapkan Content Security Policy (CSP) modern yang memperbolehkan 'unsafe-eval' untuk Alpine.js
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://www.youtube.com https://www.youtube-nocookie.com; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://unpkg.com; " .
               "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
               "img-src 'self' data: blob: https: http:; " .
               "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com https://maps.google.com https://www.google.com; " .
               "connect-src 'self' https: http:; " .
               "object-src 'none'; " .
               "base-uri 'self';";

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
