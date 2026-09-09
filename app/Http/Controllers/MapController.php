<?php

namespace App\Http\Controllers;

use App\Models\Dusun;
use App\Models\PublicFacility;
use Illuminate\Support\Facades\Cache;

class MapController extends Controller
{
    public function index()
    {
        // Get all dusuns with geojson mapped (cached)
        $dusuns = Cache::remember('map_dusuns', 3600, function () {
            return Dusun::withCount([
                'citizens' => function ($query) {
                    $query->where('status', 'Aktif');
                },
                'families',
            ])->get();
        });

        $facilities = Cache::remember('map_facilities', 3600, function () {
            return PublicFacility::all();
        });

        return view('pages.peta', compact('dusuns', 'facilities'));
    }
}
