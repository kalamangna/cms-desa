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
use App\Models\Family;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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
        
        $totalPenduduk = Cache::remember('home_total_penduduk_real', $ttl, function () {
            return \App\Models\Citizen::where('status', 'Aktif')->count();
        });

        $totalUMKM = Cache::remember('home_total_umkm', $ttl, function () use ($latestYear) {
            return StatisticData::whereHas('indicator', function($q) {
                $q->where('name', 'Jumlah Unit UMKM');
            })->where('year', $latestYear)->value('value') ?? 0;
        });

        $totalDusun = Cache::remember('home_total_dusun', $ttl, function () {
            return \App\Models\Dusun::count();
        });

        $totalKeluarga = Cache::remember('home_total_keluarga', $ttl, function () {
            return Family::count();
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

        $lakiLakiCount = Cache::remember('home_laki_laki_count', $ttl, function () {
            return \App\Models\Citizen::where('status', 'Aktif')->where('gender', 'Laki-laki')->count();
        });

        $perempuanCount = Cache::remember('home_perempuan_count', $ttl, function () {
            return \App\Models\Citizen::where('status', 'Aktif')->where('gender', 'Perempuan')->count();
        });

        $useCitizenData = true; // flag to tell view which format to use

        $currentYear = date('Y');
        $budgetSummary = Cache::remember('home_budget_summary', $ttl, function () use ($currentYear) {
            $categories = BudgetCategory::all();
            $summary = [
                'pendapatan' => ['budget' => 0, 'realization' => 0],
                'belanja' => ['budget' => 0, 'realization' => 0],
                'pembiayaan' => ['budget' => 0, 'realization' => 0]
            ];

            foreach ($categories as $cat) {
                $budget = BudgetRealization::where('budget_category_id', $cat->id)
                    ->where('year', $currentYear)
                    ->sum('budget_amount');
                $real = BudgetRealization::where('budget_category_id', $cat->id)
                    ->where('year', $currentYear)
                    ->sum('realization_amount');

                if ($cat->slug === 'pendapatan') {
                    $summary['pendapatan']['budget'] += $budget;
                    $summary['pendapatan']['realization'] += $real;
                } elseif ($cat->slug === 'belanja') {
                    $summary['belanja']['budget'] += $budget;
                    $summary['belanja']['realization'] += $real;
                } elseif ($cat->slug === 'pembiayaan') {
                    $summary['pembiayaan']['budget'] += $budget;
                    $summary['pembiayaan']['realization'] += $real;
                }
            }

            return $summary;
        });

        $belanjaDetails = Cache::remember('home_belanja_details', $ttl, function () use ($currentYear) {
            return BudgetRealization::where('year', $currentYear)
                ->whereHas('category', function ($q) {
                    $q->where('slug', 'belanja');
                })
                ->get();
        });

        // Kalkulasi persentase APBDes (dipindahkan dari Blade)
        $pendapatanPct = $budgetSummary['pendapatan']['budget'] > 0
            ? min(($budgetSummary['pendapatan']['realization'] / $budgetSummary['pendapatan']['budget']) * 100, 100)
            : 0;
        $belanjaPct = $budgetSummary['belanja']['budget'] > 0
            ? min(($budgetSummary['belanja']['realization'] / $budgetSummary['belanja']['budget']) * 100, 100)
            : 0;

        // Preparasi data Chart.js (aman dengan json_encode, dipindahkan dari Blade)
        $donutPalette = ['#10b981','#0ea5e9','#f59e0b','#6366f1','#ec4899','#8b5cf6','#06b6d4','#14b8a6','#f97316','#3b82f6'];
        $belanjaChartLabels = $belanjaDetails->map(fn ($d) => Str::limit($d->title, 20))->values();
        $belanjaChartData   = $belanjaDetails->pluck('realization_amount')->values();
        $belanjaChartColors = $belanjaDetails->keys()->map(fn ($i) => $donutPalette[$i % count($donutPalette)])->values();

        $publications = Cache::remember('home_publications', $ttl, function () {
            return Publication::latest()->take(4)->get();
        });

        $galleries = Cache::remember('home_galleries', $ttl, function () {
            return Gallery::latest()->take(6)->get();
        });

        return view('home', compact(
            'featuredPost',
            'recentPosts',
            'announcements',
            'villageHead',
            'totalPenduduk',
            'totalKeluarga',
            'totalUMKM',
            'totalDusun',
            'latestYear',
            'jobData',
            'eduData',
            'useCitizenData',
            'budgetSummary',
            'belanjaDetails',
            'pendapatanPct',
            'belanjaPct',
            'belanjaChartLabels',
            'belanjaChartData',
            'belanjaChartColors',
            'publications',
            'galleries',
            'lakiLakiCount',
            'perempuanCount'
        ));
    }
}
