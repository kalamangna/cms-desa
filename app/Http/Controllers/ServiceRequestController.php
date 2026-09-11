<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => ['required', 'numeric', 'digits:16'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s()]{8,25}$/'],
            'service_id' => ['required', 'exists:services,id'],
        ], [
            'nik.digits' => 'NIK harus berjumlah persis 16 digit angka.',
            'nik.numeric' => 'NIK hanya boleh berisi angka.',
            'phone.regex' => 'Format nomor WhatsApp/telepon tidak valid (minimal 8 digit angka).',
        ]);

        $clean = fn (?string $v) => $v !== null ? trim(strip_tags(preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $v))) : null;

        $sanitized = [
            'nik' => trim($validated['nik']),
            'name' => $clean($validated['name']),
            'phone' => preg_replace('/[^0-9+]/', '', $validated['phone']),
            'service_id' => $validated['service_id'],
        ];

        $serviceRequest = ServiceRequest::create($sanitized);

        return redirect()->route('layanan')
            ->with('success', 'Terkirim!')
            ->with('ticket_number', $serviceRequest->ticket_number);
    }

    public function track(Request $request)
    {
        $request->validate([
            'ticket_number' => ['required', 'string', 'max:30'],
        ]);

        $serviceRequest = ServiceRequest::with('service')
            ->where('ticket_number', trim($request->ticket_number))
            ->first();

        if ($request->wantsJson() || $request->ajax()) {
            if (! $serviceRequest) {
                return response()->json(['found' => false, 'message' => 'Nomor tiket tidak ditemukan'], 404);
            }

            return response()->json([
                'found' => true,
                'ticket_number' => $serviceRequest->ticket_number,
                'name' => $serviceRequest->name,
                'nik_masked' => substr($serviceRequest->nik, 0, 4).'**********',
                'service_title' => $serviceRequest->service?->title ?? 'Layanan Umum',
                'status' => $serviceRequest->status,
                'created_at' => $serviceRequest->created_at->translatedFormat('d M Y, H:i'),
            ]);
        }

        $services = Service::orderBy('id', 'asc')->get();

        return view('pages.layanan', compact('services', 'serviceRequest'))
            ->with('searched_ticket', $request->ticket_number);
    }
}
