<?php

namespace App\Http\Controllers;

use App\Models\StatisticCategory;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index()
    {
        $categories = StatisticCategory::with(['indicators.data' => function($query) {
            $query->orderBy('year', 'asc');
        }])->get();

        return view('statistics.index', compact('categories'));
    }
}
