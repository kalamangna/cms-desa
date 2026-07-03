<?php

namespace App\Http\Controllers;

use App\Models\Institution;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Institution::orderBy('name', 'asc')->get();
        return view('institutions.index', compact('institutions'));
    }
}
