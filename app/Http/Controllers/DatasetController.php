<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function index(Request $request)
    {
        $query = Dataset::latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('year', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%");
            });
        }

        $datasets = $query->paginate(10)->withQueryString();
        return view('datasets.index', compact('datasets'));
    }
}
