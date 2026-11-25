<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\View\View;

class LetterVerificationController extends Controller
{
    public function show(string $token): View
    {
        $serviceRequest = ServiceRequest::with('service')
            ->where('verification_token', trim($token))
            ->whereIn('tte_status', [ServiceRequest::TTE_SIGNED, ServiceRequest::TTE_REVOKED])
            ->firstOrFail();

        return view('pages.verifikasi-surat', [
            'serviceRequest' => $serviceRequest,
        ]);
    }
}
