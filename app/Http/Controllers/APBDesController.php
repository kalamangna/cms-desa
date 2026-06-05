<?php

namespace App\Http\Controllers;

use App\Models\BudgetCategory;
use Illuminate\Http\Request;

class APBDesController extends Controller
{
    public function index()
    {
        $categories = BudgetCategory::with('realizations')->get();
        return view('apbdes.index', compact('categories'));
    }
}
