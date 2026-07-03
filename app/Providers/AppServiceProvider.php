<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        // Cache Invalidation Observers
        $clearHomeCache = function () {
            Cache::forget('home_posts');
            Cache::forget('home_announcements');
            Cache::forget('home_village_head');
            Cache::forget('home_job_data');
            Cache::forget('home_edu_data');
            Cache::forget('home_budget_summary');
            Cache::forget('home_belanja_details');
            Cache::forget('home_publications');
            Cache::forget('home_galleries');
            Cache::forget('home_total_dusun');
            Cache::forget('profil_total_dusun');
        };

        \App\Models\Post::saved($clearHomeCache);
        \App\Models\Post::deleted($clearHomeCache);
        \App\Models\Announcement::saved($clearHomeCache);
        \App\Models\Announcement::deleted($clearHomeCache);
        \App\Models\Official::saved($clearHomeCache);
        \App\Models\Official::deleted($clearHomeCache);
        \App\Models\StatisticData::saved($clearHomeCache);
        \App\Models\StatisticData::deleted($clearHomeCache);
        \App\Models\BudgetRealization::saved($clearHomeCache);
        \App\Models\BudgetRealization::deleted($clearHomeCache);
        \App\Models\Publication::saved($clearHomeCache);
        \App\Models\Publication::deleted($clearHomeCache);
        \App\Models\Gallery::saved($clearHomeCache);
        \App\Models\Gallery::deleted($clearHomeCache);
        \App\Models\Dusun::saved($clearHomeCache);
        \App\Models\Dusun::deleted($clearHomeCache);

        try {
            if (Schema::hasTable('settings')) {
                $settings = Setting::pluck('value', 'key')->all();
                View::share('site_settings', $settings);
            }

            if (Schema::hasTable('visitor_logs')) {
                $visitorStats = Cache::remember('visitor_stats_summary', 300, function () {
                    $todayStr = now()->toDateString();
                    $yesterdayStr = now()->subDay()->toDateString();

                    $today = \App\Models\VisitorLog::where('visit_date', $todayStr)
                        ->distinct('ip_hash')
                        ->count('ip_hash');

                    $yesterday = \App\Models\VisitorLog::where('visit_date', $yesterdayStr)
                        ->distinct('ip_hash')
                        ->count('ip_hash');

                    $total = \App\Models\VisitorLog::distinct('ip_hash')
                        ->count('ip_hash');

                    return [
                        'today' => $today,
                        'yesterday' => $yesterday,
                        'total' => $total,
                    ];
                });
                View::share('visitor_stats', $visitorStats);
            }
        } catch (\Throwable $e) {
            // Database not ready or migrations not run yet, safe to ignore during boot
        }
    }
}
