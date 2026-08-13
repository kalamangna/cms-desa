<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\BudgetCategory;
use App\Models\BudgetRealization;
use App\Models\Citizen;
use App\Models\Dusun;
use App\Models\Family;
use App\Models\Gallery;
use App\Models\Official;
use App\Models\Post;
use App\Models\Publication;
use App\Models\StatisticData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Cache TTL: 1 Hour
        $ttl = 3600;

        $allPosts = Cache::remember('home_posts', $ttl, function () {
            return Post::with('category')->latest()->where('published_at', '<=', now())->take(4)->get();
        });
        $featuredPost = $allPosts->first();
        $recentPosts = $allPosts->skip(1);

        $announcements = Cache::remember('home_announcements', $ttl, function () {
            return Announcement::latest()->where('published_at', '<=', now())->take(5)->get();
        });

        $villageHead = Cache::remember('home_village_head', $ttl * 24, function () {
            return Official::where('level', 1)->orderBy('order')->first();
        });

        $latestYear = Cache::remember('home_latest_year', $ttl, function () {
            return StatisticData::max('year') ?? date('Y');
        });

        $totalPenduduk = Cache::remember('home_total_penduduk_real', $ttl, function () {
            return Citizen::where('status', 'Aktif')->count();
        });

        $totalUMKM = Cache::remember('home_total_umkm', $ttl, function () use ($latestYear) {
            return StatisticData::whereHas('indicator', function ($q) {
                $q->where('name', 'Jumlah Unit UMKM');
            })->where('year', $latestYear)->value('value') ?? 0;
        });

        $totalDusun = Cache::remember('home_total_dusun', $ttl, function () {
            return Dusun::count();
        });

        $totalKeluarga = Cache::remember('home_total_keluarga', $ttl, function () {
            return Family::count();
        });

        $totalRT = Cache::remember('home_total_rt', $ttl, function () {
            return Dusun::sum('total_rt');
        });

        $totalRW = Cache::remember('home_total_rw', $ttl, function () {
            return Dusun::sum('total_rw');
        });

        $jobData = Cache::remember('home_job_stats', $ttl, function () {
            return Citizen::select('job_status as name', DB::raw('count(*) as total'))
                ->where('status', 'Aktif')
                ->whereNotNull('job_status')
                ->where('job_status', '!=', '')
                ->groupBy('job_status')
                ->get();
        });

        $eduData = Cache::remember('home_edu_stats', $ttl, function () {
            return Citizen::select('education as name', DB::raw('count(*) as total'))
                ->where('status', 'Aktif')
                ->whereNotNull('education')
                ->groupBy('education')
                ->get();
        });

        $lakiLakiCount = Cache::remember('home_laki_laki_count', $ttl, function () {
            return Citizen::where('status', 'Aktif')->where('gender', 'Laki-laki')->count();
        });

        $perempuanCount = Cache::remember('home_perempuan_count', $ttl, function () {
            return Citizen::where('status', 'Aktif')->where('gender', 'Perempuan')->count();
        });

        $disabilitasCount = Cache::remember('home_disabilitas_count', $ttl, function () {
            return Citizen::where('status', 'Aktif')
                ->where(function ($q) {
                    $q->where('disability_physical', 1)
                        ->orWhere('disability_mental', 1)
                        ->orWhere('disability_intellectual', 1)
                        ->orWhere('disability_blind', 1)
                        ->orWhere('disability_deaf', 1)
                        ->orWhere('disability_speech', 1);
                })->count();
        });

        $useCitizenData = true; // flag to tell view which format to use

        $currentYear = date('Y');
        $budgetSummary = Cache::remember("home_budget_summary_{$currentYear}", $ttl, function () use ($currentYear) {
            $categories = BudgetCategory::all();
            $summary = [
                'pendapatan' => ['budget' => 0, 'realization' => 0],
                'belanja' => ['budget' => 0, 'realization' => 0],
                'pembiayaan' => ['budget' => 0, 'realization' => 0],
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

        $belanjaDetails = Cache::remember("home_belanja_details_{$currentYear}", $ttl, function () use ($currentYear) {
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
            'totalKeluarga',
            'totalUMKM',
            'totalDusun',
            'totalRT',
            'totalRW',
            'latestYear',
            'jobData',
            'eduData',
            'useCitizenData',
            'budgetSummary',
            'belanjaDetails',
            'pendapatanPct',
            'belanjaPct',

            'publications',
            'galleries',
            'lakiLakiCount',
            'perempuanCount',
            'disabilitasCount'
        ));
    }
}
