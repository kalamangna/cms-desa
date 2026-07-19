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

        $totalRt = \Illuminate\Support\Facades\Cache::remember('profil_total_rt', 3600, function () {
            return (int) \App\Models\Dusun::sum('total_rt');
        });

        $totalRw = \Illuminate\Support\Facades\Cache::remember('profil_total_rw', 3600, function () {
            return (int) \App\Models\Dusun::sum('total_rw');
        });

        return view('pages.profil', compact('totalDusun', 'totalPenduduk', 'totalRt', 'totalRw'));
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

    public function potensi()
    {
        $potentials = \App\Models\VillagePotential::where('is_active', true)->latest()->get();
        return view('pages.potensi', compact('potentials'));
    }
}
