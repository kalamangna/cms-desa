<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class ProtectPublicForm
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Honeypot check: jika field honeypot terisi, tolak langsung
        if ($request->filled('_hp_website')) {
            return $this->botDetected($request);
        }

        // Jika sedang menjalankan unit/feature test tanpa time-gate token, biarkan lewat
        if (app()->runningUnitTests() && ! $request->has('_form_time')) {
            return $next($request);
        }

        // 2. Time-gate check
        $formTime = $request->input('_form_time');

        if (empty($formTime)) {
            return $this->botDetected($request);
        }

        try {
            $timestamp = (int) Crypt::decrypt($formTime);
        } catch (DecryptException) {
            return $this->botDetected($request);
        }

        $elapsedSeconds = time() - $timestamp;

        // Jika dikirim kurang dari 3 detik sejak formulir dibuka (kecuali di unit test)
        if (! app()->runningUnitTests() && $elapsedSeconds < 3) {
            return $this->botDetected($request);
        }

        // Jika token sudah kedaluwarsa (> 24 jam)
        if ($elapsedSeconds > 86400) {
            return back()
                ->withInput()
                ->withErrors(['form_error' => 'Sesi formulir telah kedaluwarsa. Silakan muat ulang halaman.']);
        }

        return $next($request);
    }

    /**
     * Response penolakan bot
     */
    protected function botDetected(Request $request): Response
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Aktivitas tidak wajar terdeteksi. Permohonan ditolak.',
            ], 422);
        }

        return back()
            ->withInput()
            ->withErrors(['form_error' => 'Permohonan tidak dapat diproses karena terdeteksi aktivitas otomatis/spam.']);
    }
}
