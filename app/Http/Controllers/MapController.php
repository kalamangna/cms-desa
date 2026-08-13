<?php

namespace App\Http\Controllers;

use App\Models\Dusun;
use App\Models\PublicFacility;
use App\Models\Setting;

class MapController extends Controller
{
    public function index()
    {
        // Get all dusuns with geojson mapped
        $dusuns = Dusun::withCount([
            'citizens' => function ($query) {
                $query->where('status', 'Aktif');
            },
            'families',
        ])->get();

        // Get general settings for map center coordinates
        $site_settings = Setting::pluck('value', 'key')->toArray();

        $facilities = PublicFacility::all();

        return view('pages.peta', compact('dusuns', 'site_settings', 'facilities'));
    }
}
