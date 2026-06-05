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
        return view('pages.layanan');
    }

    public function kontak()
    {
        return view('pages.kontak');
    }
}
