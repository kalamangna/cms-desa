<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    public function index()
    {
        $publications = Publication::latest()->paginate(12);
        return view('publications.index', compact('publications'));
    }
}
