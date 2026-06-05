<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
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

        // Force Locale to ID
        config(['app.locale' => 'id']);
        config(['app.fallback_locale' => 'id']);
        \Carbon\Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID', 'id', 'indonesian');

        if (Schema::hasTable('settings')) {
            $settings = Setting::pluck('value', 'key')->all();
            View::share('site_settings', $settings);
        }
    }
}
