<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Announcement;
use App\Models\Official;
use App\Models\StatisticData;
use App\Models\StatisticCategory;
use App\Models\BudgetRealization;
use App\Models\BudgetCategory;
use App\Models\Publication;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cache TTL: 1 Hour
        $ttl = 3600;

        $allPosts = Cache::remember('home_posts', $ttl, function () {
            return Post::with('category')->latest()->where('published_at', '<=', now())->take(7)->get();
        });
        $featuredPost = $allPosts->first();
        $recentPosts = $allPosts->skip(1);

        $announcements = Cache::remember('home_announcements', $ttl, function () {
            return Announcement::latest()->where('published_at', '<=', now())->take(5)->get();
        });

        $villageHead = Cache::remember('home_village_head', $ttl * 24, function () {
            return Official::where('position', 'LIKE', '%Kepala Desa%')->first();
        });

        $latestYear = Cache::remember('home_latest_year', $ttl, function () {
            return StatisticData::max('year') ?? date('Y');
        });
        
        $totalPenduduk = Cache::remember('home_total_penduduk_real', $ttl, function () use ($latestYear) {
            $count = \App\Models\Citizen::where('status', 'Aktif')->count();
            if ($count > 0) return $count;
            return StatisticData::whereHas('indicator', function($q) {
                $q->whereIn('name', ['Jumlah Laki-laki', 'Jumlah Perempuan']);
            })->where('year', $latestYear)->sum('value');
        });

        $totalUMKM = Cache::remember('home_total_umkm', $ttl, function () use ($latestYear) {
            return StatisticData::whereHas('indicator', function($q) {
                $q->where('name', 'Jumlah Unit UMKM');
            })->where('year', $latestYear)->value('value') ?? 0;
        });

        $jobData = Cache::remember('home_job_stats', $ttl, function () {
            return \App\Models\Citizen::select('job as name', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                   ->where('status', 'Aktif')
                   ->whereNotNull('job')
                   ->groupBy('job')
                   ->get();
        });

        $eduData = Cache::remember('home_edu_stats', $ttl, function () {
            return \App\Models\Citizen::select('education as name', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                   ->where('status', 'Aktif')
                   ->whereNotNull('education')
                   ->groupBy('education')
                   ->get();
        });

        $useCitizenData = true; // flag to tell view which format to use

        $currentYear = date('Y');
        $budgetSummary = Cache::remember('home_budget_summary', $ttl, function () use ($currentYear) {
            return [
                'pendapatan' => [
                    'budget' => BudgetRealization::whereHas('category', fn($q) => $q->where('slug', 'pendapatan'))
                        ->where('year', $currentYear)->sum('budget_amount'),
                    'realization' => BudgetRealization::whereHas('category', fn($q) => $q->where('slug', 'pendapatan'))
                        ->where('year', $currentYear)->sum('realization_amount'),
                ],
                'belanja' => [
                    'budget' => BudgetRealization::whereHas('category', fn($q) => $q->where('slug', 'belanja'))
                        ->where('year', $currentYear)->sum('budget_amount'),
                    'realization' => BudgetRealization::whereHas('category', fn($q) => $q->where('slug', 'belanja'))
                        ->where('year', $currentYear)->sum('realization_amount'),
                ],
            ];
        });

        $belanjaDetails = Cache::remember('home_belanja_details', $ttl, function () use ($currentYear) {
            return BudgetRealization::whereHas('category', fn($q) => $q->where('slug', 'belanja'))
                ->where('year', $currentYear)
                ->get(['title', 'realization_amount']);
        });

        $publications = Cache::remember('home_publications', $ttl, function () {
            return Publication::latest()->take(4)->get();
        });

        $galleries = Cache::remember('home_galleries', $ttl, function () {
            return Gallery::latest()->take(8)->get();
        });

        return view('home', compact(
            'featuredPost', 
            'recentPosts', 
            'announcements', 
            'villageHead',
            'totalPenduduk',
            'totalUMKM',
            'latestYear',
            'jobData',
            'eduData',
            'useCitizenData',
            'budgetSummary',
            'belanjaDetails',
            'publications',
            'galleries'
        ));
    }
}
