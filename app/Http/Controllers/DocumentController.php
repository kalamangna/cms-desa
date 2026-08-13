<?php

namespace App\Http\Controllers;

use App\Models\Document;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::latest()->paginate(10);

        return view('documents.index', compact('documents'));
    }
}
