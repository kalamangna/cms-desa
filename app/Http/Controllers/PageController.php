<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\Dusun;
use App\Models\Service;
use App\Models\VillagePotential;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function profil()
    {
        $totalDusun = Cache::remember('profil_total_dusun', 3600, function () {
            return Dusun::count();
        });

        $totalPenduduk = Cache::remember('profil_total_penduduk', 3600, function () {
            return Citizen::where('status', 'Aktif')->count();
        });

        $totalRt = Cache::remember('profil_total_rt', 3600, function () {
            return (int) Dusun::sum('total_rt');
        });

        $totalRw = Cache::remember('profil_total_rw', 3600, function () {
            return (int) Dusun::sum('total_rw');
        });

        return view('pages.profil', compact('totalDusun', 'totalPenduduk', 'totalRt', 'totalRw'));
    }

    public function layanan()
    {
        $services = Service::orderBy('id', 'asc')->get();

        return view('pages.layanan', compact('services'));
    }

    public function kontak()
    {
        return view('pages.kontak');
    }

    public function potensi()
    {
        $potentials = VillagePotential::where('is_active', true)->latest()->get();

        return view('pages.potensi', compact('potentials'));
    }
}
