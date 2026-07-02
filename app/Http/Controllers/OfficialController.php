<?php

namespace App\Http\Controllers;

use App\Models\Official;
use Illuminate\Http\Request;

class OfficialController extends Controller
{
    public function index()
    {
        $officials = Official::orderBy('id', 'asc')->get();
        return view('officials.index', compact('officials'));
    }
}
