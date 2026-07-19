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
            'name' => 'required|string|max:255',
            'institution_address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'purpose' => 'required|string',
        ]);

        GuestBook::create($validated);

        return redirect()->back()->with('success', 'Terima kasih! Data kunjungan Anda berhasil disimpan ke dalam Buku Tamu.');
    }
}
