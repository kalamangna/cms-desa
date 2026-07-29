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

        // Terapkan Content Security Policy (CSP) modern yang memperbolehkan Vite Dev Server & 'unsafe-eval' untuk Alpine.js
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://*:5173 https:; " .
               "style-src 'self' 'unsafe-inline' http://*:5173 https:; " .
               "font-src 'self' data: https:; " .
               "img-src 'self' data: blob: https: http:; " .
               "frame-src 'self' https:; " .
               "connect-src 'self' https: http: ws://*:5173 wss://*:5173; " .
               "object-src 'none'; " .
               "base-uri 'self';";

        $response->headers->set('Content-Security-Policy', $csp, true);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
