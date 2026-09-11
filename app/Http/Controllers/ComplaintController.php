<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        return view('pages.pengaduan');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s()]{8,25}$/'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:5000'],
        ], [
            'phone.regex' => 'Format nomor WhatsApp/telepon tidak valid (minimal 8-16 digit angka).',
        ]);

        $clean = fn (?string $v) => $v !== null ? trim(strip_tags(preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $v))) : null;

        $sanitized = [
            'name' => $clean($validated['name']),
            'phone' => preg_replace('/[^0-9+]/', '', $validated['phone']),
            'title' => $clean($validated['title']),
            'content' => $clean($validated['content']),
        ];

        $complaint = Complaint::create($sanitized);

        return redirect()->route('complaints.index')
            ->with('success', 'Terkirim!')
            ->with('ticket_number', $complaint->ticket_number);
    }

    public function track(Request $request)
    {
        $request->validate([
            'ticket_number' => ['required', 'string', 'max:30'],
        ]);

        $complaint = Complaint::where('ticket_number', trim($request->ticket_number))->first();

        if ($request->wantsJson() || $request->ajax()) {
            if (! $complaint) {
                return response()->json(['found' => false, 'message' => 'Nomor tiket tidak ditemukan'], 404);
            }

            return response()->json([
                'found' => true,
                'ticket_number' => $complaint->ticket_number,
                'title' => $complaint->title,
                'content' => $complaint->content,
                'status' => $complaint->status,
                'response' => $complaint->response,
                'created_at' => $complaint->created_at->translatedFormat('d M Y, H:i'),
                'updated_at' => $complaint->updated_at->translatedFormat('d M Y, H:i'),
            ]);
        }

        return view('pages.pengaduan', compact('complaint'))->with('searched_ticket', $request->ticket_number);
    }
}
