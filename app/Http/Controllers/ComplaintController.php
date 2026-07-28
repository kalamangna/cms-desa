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
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $complaint = Complaint::create($validated);

        return redirect()->route('complaints.index')
            ->with('success', 'Pengaduan Anda berhasil dikirim!')
            ->with('ticket_number', $complaint->ticket_number);
    }

    public function track(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required|string',
        ]);

        $complaint = Complaint::where('ticket_number', trim($request->ticket_number))->first();

        if ($request->wantsJson() || $request->ajax()) {
            if (!$complaint) {
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
