<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function profil()
    {
        return view('pages.profil');
    }

    public function layanan()
    {
        $services = \App\Models\Service::orderBy('id', 'asc')->get();
        return view('pages.layanan', compact('services'));
    }

    public function kontak()
    {
        return view('pages.kontak');
    }
}
