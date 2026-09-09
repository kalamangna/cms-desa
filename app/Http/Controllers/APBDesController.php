<?php

namespace App\Http\Controllers;

use App\Models\BudgetCategory;
use Illuminate\Support\Facades\Cache;

class APBDesController extends Controller
{
    public function index()
    {
        $categories = Cache::remember('apbdes_categories', 3600, function () {
            return BudgetCategory::with('realizations')->get();
        });

        return view('apbdes.index', compact('categories'));
    }
}
