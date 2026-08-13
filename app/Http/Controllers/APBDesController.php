<?php

namespace App\Http\Controllers;

use App\Models\BudgetCategory;

class APBDesController extends Controller
{
    public function index()
    {
        $categories = BudgetCategory::with('realizations')->get();

        return view('apbdes.index', compact('categories'));
    }
}
