<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\BudgetRealization;
use App\Models\Citizen;
use App\Models\Dusun;
use App\Models\Family;
use App\Models\Gallery;
use App\Models\Official;
use App\Models\PopupInfographic;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Konsolidasi seluruh data beranda ke satu payload cache agar warm request hanya 1 query cache
        $ttl = 3600;
        $currentYear = date('Y');

        $data = Cache::remember("home_page_data_{$currentYear}", $ttl, function () use ($currentYear) {
            $allPosts = Post::with('category:id,name,slug')
                ->latest()
                ->where('published_at', '<=', now())
                ->take(4)
                ->get();

            $announcements = Announcement::latest()
                ->where('published_at', '<=', now())
                ->take(5)
                ->get();

            $villageHead = Official::where('level', 1)
                ->orderBy('order')
                ->first();

            $dusunAgg = Dusun::selectRaw('COUNT(*) as total_dusun, COALESCE(SUM(total_rt), 0) as total_rt, COALESCE(SUM(total_rw), 0) as total_rw')
                ->first();

            $totalKeluarga = Family::count();

            $citizenAgg = Citizen::where('status', 'Aktif')
                ->selectRaw("COUNT(*) as total, COALESCE(SUM(CASE WHEN gender = 'Laki-laki' THEN 1 ELSE 0 END), 0) as laki_laki, COALESCE(SUM(CASE WHEN gender = 'Perempuan' THEN 1 ELSE 0 END), 0) as perempuan")
                ->first();

            $jobData = Citizen::select('job_status as name', DB::raw('count(*) as total'))
                ->where('status', 'Aktif')
                ->whereNotNull('job_status')
                ->where('job_status', '!=', '')
                ->groupBy('job_status')
                ->get();

            $eduData = Citizen::select('education as name', DB::raw('count(*) as total'))
                ->where('status', 'Aktif')
                ->whereNotNull('education')
                ->where('education', '!=', '')
                ->groupBy('education')
                ->get();

            $budgetSummary = [
                'pendapatan' => ['budget' => 0, 'realization' => 0],
                'belanja' => ['budget' => 0, 'realization' => 0],
                'pembiayaan' => ['budget' => 0, 'realization' => 0],
            ];

            $aggregates = BudgetRealization::where('budget_realizations.year', $currentYear)
                ->join('budget_categories', 'budget_realizations.budget_category_id', '=', 'budget_categories.id')
                ->groupBy('budget_categories.slug')
                ->select(
                    'budget_categories.slug',
                    DB::raw('SUM(budget_realizations.budget_amount) as total_budget'),
                    DB::raw('SUM(budget_realizations.realization_amount) as total_realization')
                )
                ->get();

            foreach ($aggregates as $row) {
                if (isset($budgetSummary[$row->slug])) {
                    $budgetSummary[$row->slug]['budget'] = (float) $row->total_budget;
                    $budgetSummary[$row->slug]['realization'] = (float) $row->total_realization;
                }
            }

            $pendapatanPct = $budgetSummary['pendapatan']['budget'] > 0
                ? min(($budgetSummary['pendapatan']['realization'] / $budgetSummary['pendapatan']['budget']) * 100, 100)
                : 0;
            $belanjaPct = $budgetSummary['belanja']['budget'] > 0
                ? min(($budgetSummary['belanja']['realization'] / $budgetSummary['belanja']['budget']) * 100, 100)
                : 0;

            $galleries = Gallery::latest()->take(8)->get();

            $popups = PopupInfographic::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get(['image', 'title'])
                ->toArray();

            return [
                'featuredPost' => $allPosts->first(),
                'recentPosts' => $allPosts->skip(1)->values(),
                'announcements' => $announcements,
                'villageHead' => $villageHead,
                'totalPenduduk' => (int) ($citizenAgg->total ?? 0),
                'totalKeluarga' => (int) $totalKeluarga,
                'totalDusun' => (int) ($dusunAgg->total_dusun ?? 0),
                'totalRT' => (int) ($dusunAgg->total_rt ?? 0),
                'totalRW' => (int) ($dusunAgg->total_rw ?? 0),
                'jobData' => $jobData,
                'eduData' => $eduData,
                'budgetSummary' => $budgetSummary,
                'pendapatanPct' => $pendapatanPct,
                'belanjaPct' => $belanjaPct,
                'galleries' => $galleries,
                'popups' => $popups,
                'lakiLakiCount' => (int) ($citizenAgg->laki_laki ?? 0),
                'perempuanCount' => (int) ($citizenAgg->perempuan ?? 0),
            ];
        });

        return view('home', $data);
    }
}
