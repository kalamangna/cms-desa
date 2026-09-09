<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Support\Facades\Cache;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Cache::remember('institutions_list', 3600, function () {
            return Institution::orderBy('name', 'asc')->get();
        });

        return view('institutions.index', compact('institutions'));
    }
}
