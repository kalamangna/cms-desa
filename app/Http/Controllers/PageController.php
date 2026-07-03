<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function profil()
    {
        $totalDusun = \Illuminate\Support\Facades\Cache::remember('profil_total_dusun', 3600, function () {
            return \App\Models\Dusun::count();
        });

        $totalPenduduk = \Illuminate\Support\Facades\Cache::remember('profil_total_penduduk', 3600, function () {
            return \App\Models\Citizen::where('status', 'Aktif')->count();
        });

        return view('pages.profil', compact('totalDusun', 'totalPenduduk'));
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
