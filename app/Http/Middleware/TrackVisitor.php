<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\VisitorLog;
use Stevebauman\Location\Facades\Location;
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
            $userAgent = $request->userAgent() ?? '';

            // Abaikan jika request dilakukan oleh Bot / Web Crawler
            if ($this->isBot($userAgent)) {
                return $response;
            }

            // We only track GET requests that are not AJAX, not admin, not livewire, and not static files
            if ($request->isMethod('GET') &&
                !$request->ajax() &&
                !str_starts_with($path, 'admin') &&
                !str_starts_with($path, 'livewire') &&
                !str_starts_with($path, 'api') &&
                !str_starts_with($path, 'up') &&
                !str_contains($path, '.')) {

                $ip = $request->ip() ?? '127.0.0.1';
                $ipHash = hash('sha256', $ip . '|' . $userAgent);

                // Geolocation lookup
                $city = null;
                $region = null;
                $country = null;

                try {
                    // Dapatkan lokasi dari IP pengunjung
                    $position = Location::get($ip);

                    if ($position) {
                        $city = $position->cityName ?: null;
                        $region = $this->normalizeRegionName($position->regionName ?: null);
                        $country = $position->countryName ?: null;
                    }
                } catch (\Throwable $e) {
                    // Abaikan jika service geolocation tidak merespon / timeout
                }

                VisitorLog::create([
                    'ip_hash' => $ipHash,
                    'url' => '/' . ltrim($request->path(), '/'),
                    'user_agent' => substr($userAgent, 0, 255),
                    'city' => $city,
                    'region' => $region,
                    'country' => $country,
                    'visit_date' => now()->toDateString(),
                ]);
            }
        } catch (\Throwable $e) {
            // Silently ignore tracking failures so the main request doesn't crash
        }

        return $response;
    }

    /**
     * Memeriksa apakah User Agent berasal dari Bot / Crawler.
     */
    protected function isBot(string $userAgent): bool
    {
        if (empty($userAgent)) {
            return false;
        }

        $botPattern = '/(bot|googlebot|bingbot|crawler|spider|slurp|facebookexternalhit|twitterbot|bytespider|yandex|curl|wget|python|httpclient)/i';

        return (bool) preg_match($botPattern, $userAgent);
    }

    /**
     * Menormalisasi nama provinsi bahasa Inggris ke Bahasa Indonesia.
     */
    protected function normalizeRegionName(?string $region): ?string
    {
        if (!$region) {
            return null;
        }

        $map = [
            'South Sulawesi' => 'Sulawesi Selatan',
            'North Sulawesi' => 'Sulawesi Utara',
            'Central Sulawesi' => 'Sulawesi Tengah',
            'Southeast Sulawesi' => 'Sulawesi Tenggara',
            'West Sulawesi' => 'Sulawesi Barat',
            'Gorontalo' => 'Gorontalo',
            'Special Region of Yogyakarta' => 'DI Yogyakarta',
            'Yogyakarta' => 'DI Yogyakarta',
            'Jakarta' => 'DKI Jakarta',
            'Special Capital Region of Jakarta' => 'DKI Jakarta',
            'West Java' => 'Jawa Barat',
            'Central Java' => 'Jawa Tengah',
            'East Java' => 'Jawa Timur',
            'Banten' => 'Banten',
            'Bali' => 'Bali',
            'West Nusa Tenggara' => 'Nusa Tenggara Barat',
            'East Nusa Tenggara' => 'Nusa Tenggara Timur',
            'West Kalimantan' => 'Kalimantan Barat',
            'South Kalimantan' => 'Kalimantan Selatan',
            'Central Kalimantan' => 'Kalimantan Tengah',
            'East Kalimantan' => 'Kalimantan Timur',
            'North Kalimantan' => 'Kalimantan Utara',
            'North Sumatra' => 'Sumatera Utara',
            'West Sumatra' => 'Sumatera Barat',
            'South Sumatra' => 'Sumatera Selatan',
            'Aceh' => 'Aceh',
            'Riau' => 'Riau',
            'Riau Islands' => 'Kepulauan Riau',
            'Jambi' => 'Jambi',
            'Bengkulu' => 'Bengkulu',
            'Lampung' => 'Lampung',
            'Bangka Belitung' => 'Kepulauan Bangka Belitung',
            'Bangka-Belitung Islands' => 'Kepulauan Bangka Belitung',
            'Maluku' => 'Maluku',
            'North Maluku' => 'Maluku Utara',
            'Papua' => 'Papua',
            'West Papua' => 'Papua Barat',
        ];

        return $map[$region] ?? $region;
    }
}
