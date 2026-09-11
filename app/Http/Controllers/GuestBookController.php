<?php

namespace App\Http\Controllers;

use App\Models\GuestBook;
use Illuminate\Http\Request;

class GuestBookController extends Controller
{
    public function index()
    {
        return view('pages.buku_tamu');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'institution_address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s()]{8,25}$/'],
            'purpose' => ['required', 'string', 'max:2000'],
        ], [
            'phone.regex' => 'Format nomor kontak/telepon tidak valid (minimal 8 digit angka).',
        ]);

        $clean = fn (?string $v) => $v !== null ? trim(strip_tags(preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $v))) : null;

        $sanitized = [
            'name' => $clean($validated['name']),
            'institution_address' => $clean($validated['institution_address']),
            'phone' => preg_replace('/[^0-9+]/', '', $validated['phone']),
            'purpose' => $clean($validated['purpose']),
        ];

        GuestBook::create($sanitized);

        return redirect()->back()->with('success', 'Terkirim!');
    }
}
