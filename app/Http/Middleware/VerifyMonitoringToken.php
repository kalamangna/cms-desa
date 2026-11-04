<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMonitoringToken
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = config('services.monitoring.token');

        if (empty($configuredToken)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Monitoring API key is not configured on this server.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $token = $request->bearerToken() ?? $request->header('X-Monitoring-Key');

        if (! $token || ! hash_equals((string) $configuredToken, (string) $token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid monitoring token.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
