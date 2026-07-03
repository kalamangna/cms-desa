<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\VisitorLog;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Perform tracking after response has been prepared (post-middleware)
        try {
            $path = $request->path();

            // We only track GET requests that are not AJAX, not admin, not livewire, and not static files
            if ($request->isMethod('GET') &&
                !$request->ajax() &&
                !str_starts_with($path, 'admin') &&
                !str_starts_with($path, 'livewire') &&
                !str_starts_with($path, 'api') &&
                !str_starts_with($path, 'up') &&
                !str_contains($path, '.')) {

                $ip = $request->ip() ?? '127.0.0.1';
                $userAgent = $request->userAgent() ?? '';
                $ipHash = hash('sha256', $ip . '|' . $userAgent);

                VisitorLog::create([
                    'ip_hash' => $ipHash,
                    'url' => $request->fullUrl(),
                    'user_agent' => substr($userAgent, 0, 255),
                    'visit_date' => now()->toDateString(),
                ]);
            }
        } catch (\Throwable $e) {
            // Silently ignore tracking failures so the main request doesn't crash
        }

        return $response;
    }
}
