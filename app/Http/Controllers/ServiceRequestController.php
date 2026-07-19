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
            'nik' => 'required|string|max:16',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
        ]);

        $serviceRequest = ServiceRequest::create($validated);

        return redirect()->route('layanan')
            ->with('success', 'Permohonan layanan berhasil diajukan!')
            ->with('ticket_number', $serviceRequest->ticket_number);
    }

    public function track(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required|string',
        ]);

        $serviceRequest = ServiceRequest::with('service')
            ->where('ticket_number', $request->ticket_number)
            ->first();

        $services = Service::orderBy('id', 'asc')->get();

        return view('pages.layanan', compact('services', 'serviceRequest'))
            ->with('searched_ticket', $request->ticket_number);
    }
}
