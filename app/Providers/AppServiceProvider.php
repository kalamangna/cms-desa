<?php

namespace App\Providers;

use App\Filament\Components\CustomBackupDestinationListRecords;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\BudgetCategory;
use App\Models\BudgetRealization;
use App\Models\Category;
use App\Models\Citizen;
use App\Models\Dataset;
use App\Models\Dusun;
use App\Models\Family;
use App\Models\Gallery;
use App\Models\Institution;
use App\Models\Official;
use App\Models\PopupInfographic;
use App\Models\Post;
use App\Models\Publication;
use App\Models\PublicFacility;
use App\Models\Service;
use App\Models\ServiceRequirement;
use App\Models\Setting;
use App\Models\StatisticCategory;
use App\Models\StatisticData;
use App\Models\StatisticIndicator;
use App\Models\User;
use App\Models\VillagePotential;
use App\Models\VisitorLog;
use App\Notifications\SystemMonitorNotification;
use App\Services\GoogleDriveAdapterWrapper;
use App\Services\SettingChangeNotifier;
use App\Services\StatisticService;
use Filament\Forms\Components\Select;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Livewire\Livewire;
use Masbug\Flysystem\GoogleDriveAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(SettingChangeNotifier::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || str_starts_with(config('app.url', ''), 'https://')) {
            URL::forceScheme('https');
        }

        Paginator::useTailwind();

        // Register Google Drive Storage Driver
        try {
            Storage::extend('google', function ($app, $config) {
                try {
                    $options = [];
                    if (! empty($config['teamDriveId'] ?? null)) {
                        $options['teamDriveId'] = $config['teamDriveId'];
                    }

                    $folderId = $config['folder'] ?? null;
                    if (! empty($folderId)) {
                        $options['sharedFolderId'] = $folderId;
                    }

                    $client = new Client;
                    $client->setClientId($config['clientId'] ?? '');
                    $client->setClientSecret($config['clientSecret'] ?? '');
                    if (! empty($config['refreshToken'] ?? null)) {
                        $client->refreshToken($config['refreshToken']);
                    }

                    $service = new Drive($client);
                    $rawAdapter = new GoogleDriveAdapter($service, null, $options);
                    $adapter = new GoogleDriveAdapterWrapper($rawAdapter);
                    $driver = new Filesystem($adapter);

                    return new FilesystemAdapter($driver, $adapter);
                } catch (\Throwable $e) {
                    $rawAdapter = new LocalFilesystemAdapter(storage_path('app/backup-temp'));
                    $adapter = new GoogleDriveAdapterWrapper($rawAdapter);
                    $driver = new Filesystem($adapter);

                    return new FilesystemAdapter($driver, $adapter);
                }
            });
        } catch (\Throwable $e) {
        }

        // Register custom Livewire backup list records component to prevent polling expiration
        Livewire::component('custom-backup-destination-list-records', CustomBackupDestinationListRecords::class);

        // Audit Log Listeners for Auth
        Event::listen(Login::class, function ($event) {
            try {
                AuditLog::create([
                    'user_id' => $event->user?->id,
                    'user_name' => $event->user?->name ?? 'Sistem',
                    'event' => 'login',
                    'description' => "Pengguna {$event->user?->name} berhasil masuk (login) ke sistem",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Throwable $e) {
            }
        });

        Event::listen(Logout::class, function ($event) {
            try {
                if ($event->user) {
                    AuditLog::create([
                        'user_id' => $event->user->id,
                        'user_name' => $event->user->name,
                        'event' => 'logout',
                        'description' => "Pengguna {$event->user->name} keluar (logout) dari sistem",
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
            } catch (\Throwable $e) {
            }
        });

        // 1. Radar Keamanan Autentikasi (Logins) - Telegram Notification
        Event::listen(Login::class, function ($event) {
            try {
                if (app()->runningUnitTests() || app()->environment('testing') || app()->runningInConsole()) {
                    return;
                }

                // Lewati notifikasi jika yang login adalah super admin agar tidak berisik
                if ($event->user->hasRole('super_admin')) {
                    return;
                }

                $ip = request()->ip();
                $ua = request()->userAgent() ?? '-';
                $browser = strlen($ua) > 60 ? substr($ua, 0, 60).'…' : $ua;
                $roles = $event->user->getRoleNames()->implode(', ') ?: 'Tanpa role';
                $msg = "👤 {$event->user->name}\n"
                         ."🔑 {$event->user->username}\n"
                         ."🎭 {$roles}\n"
                         ."🌐 {$ip}\n"
                         ."🖥 <code>{$browser}</code>";
                Notification::route('telegram', 'system')->notify(new SystemMonitorNotification('LOGIN BERHASIL', $msg, 'success'));
            } catch (\Throwable $e) {
            }
        });

        Event::listen(Failed::class, function ($event) {
            try {
                if (app()->runningUnitTests() || app()->environment('testing') || app()->runningInConsole()) {
                    return;
                }

                $ip = request()->ip();
                $ua = request()->userAgent() ?? '-';
                $browser = strlen($ua) > 60 ? substr($ua, 0, 60).'…' : $ua;
                $username = $event->credentials['username'] ?? 'tidak diketahui';
                $msg = "🔑 {$username}\n"
                         ."🌐 {$ip}\n"
                         ."🖥 <code>{$browser}</code>";
                Notification::route('telegram', 'system')->notify(new SystemMonitorNotification('LOGIN GAGAL', $msg, 'danger'));
            } catch (\Throwable $e) {
            }
        });

        // 2. Pengawasan Hak Akses (User Management)
        User::created(function (User $user) {
            try {
                if (app()->runningUnitTests() || app()->environment('testing') || app()->runningInConsole()) {
                    return;
                }

                if (auth()->check() && auth()->user()->hasRole('super_admin')) {
                    return;
                }

                $roles = $user->getRoleNames()->implode(', ') ?: 'Tanpa role';
                $msg = "👤 {$user->name}\n"
                       ."🔑 {$user->username}\n"
                       ."🎭 {$roles}";
                Notification::route('telegram', 'system')->notify(new SystemMonitorNotification('AKUN ADMIN DIBUAT', $msg, 'warning'));
            } catch (\Throwable $e) {
            }
        });

        User::deleted(function (User $user) {
            try {
                if (app()->runningUnitTests() || app()->environment('testing') || app()->runningInConsole()) {
                    return;
                }

                if (auth()->check() && auth()->user()->hasRole('super_admin')) {
                    return;
                }

                $roles = $user->getRoleNames()->implode(', ') ?: 'Tanpa role';
                $msg = "👤 {$user->name}\n"
                       ."🔑 {$user->username}\n"
                       ."🎭 {$roles}";
                Notification::route('telegram', 'system')->notify(new SystemMonitorNotification('AKUN ADMIN DIHAPUS', $msg, 'danger'));
            } catch (\Throwable $e) {
            }
        });

        // 3. Perubahan Pengaturan Krusial (dirangkum satu notifikasi per request)
        Setting::updated(function (Setting $setting): void {
            try {
                if (app()->runningUnitTests() || app()->environment('testing') || app()->runningInConsole()) {
                    return;
                }

                if (auth()->check() && auth()->user()->hasRole('super_admin')) {
                    return;
                }

                app(SettingChangeNotifier::class)->record($setting);
            } catch (\Throwable) {
            }
        });

        app()->terminating(function (): void {
            try {
                if (app()->runningUnitTests() || app()->environment('testing') || app()->runningInConsole()) {
                    return;
                }

                app(SettingChangeNotifier::class)->flush(auth()->user()?->name);
            } catch (\Throwable) {
            }
        });

        // Cache Invalidation Observers (dibatasi per domain agar tidak memicu stampede lintas modul)
        $clearHomeData = function (): void {
            Cache::forget('home_page_data_'.date('Y'));
        };

        $clearPostCache = function () use ($clearHomeData): void {
            $clearHomeData();
            Cache::forget('posts_categories_with_count');
            Cache::forget('sitemap_posts');
            Cache::forget('sitemap_xml_content');
        };
        Post::saved($clearPostCache);
        Post::deleted($clearPostCache);
        Category::saved($clearPostCache);
        Category::deleted($clearPostCache);

        $clearAnnouncementCache = function () use ($clearHomeData): void {
            $clearHomeData();
            Cache::forget('sitemap_xml_content');
        };
        Announcement::saved($clearAnnouncementCache);
        Announcement::deleted($clearAnnouncementCache);

        $clearOfficialCache = function () use ($clearHomeData): void {
            $clearHomeData();
            Cache::forget('officials_tree_and_list');
        };
        Official::saved($clearOfficialCache);
        Official::deleted($clearOfficialCache);

        $clearInstitutionCache = function (): void {
            Cache::forget('institutions_list');
        };
        Institution::saved($clearInstitutionCache);
        Institution::deleted($clearInstitutionCache);

        $clearBudgetCache = function () use ($clearHomeData): void {
            $clearHomeData();
            Cache::forget('apbdes_categories');
        };
        BudgetRealization::saved($clearBudgetCache);
        BudgetRealization::deleted($clearBudgetCache);
        BudgetCategory::saved($clearBudgetCache);
        BudgetCategory::deleted($clearBudgetCache);

        $clearStatisticCache = function (): void {
            StatisticService::clearCache();
        };
        StatisticData::saved($clearStatisticCache);
        StatisticData::deleted($clearStatisticCache);
        StatisticCategory::saved($clearStatisticCache);
        StatisticCategory::deleted($clearStatisticCache);
        StatisticIndicator::saved($clearStatisticCache);
        StatisticIndicator::deleted($clearStatisticCache);

        $clearSitemapCache = function (): void {
            Cache::forget('sitemap_xml_content');
        };
        Publication::saved($clearSitemapCache);
        Publication::deleted($clearSitemapCache);
        Dataset::saved($clearSitemapCache);
        Dataset::deleted($clearSitemapCache);

        Gallery::saved($clearHomeData);
        Gallery::deleted($clearHomeData);

        $clearDusunCache = function () use ($clearHomeData): void {
            $clearHomeData();
            Cache::forget('profil_total_dusun');
            Cache::forget('profil_total_rt');
            Cache::forget('profil_total_rw');
            Cache::forget('map_dusuns');
            StatisticService::clearCache();
        };
        Dusun::saved($clearDusunCache);
        Dusun::deleted($clearDusunCache);

        $clearPopulationCache = function () use ($clearHomeData): void {
            $clearHomeData();
            Cache::forget('profil_total_penduduk');
            StatisticService::clearCache();
        };
        Citizen::saved($clearPopulationCache);
        Citizen::deleted($clearPopulationCache);
        Family::saved($clearPopulationCache);
        Family::deleted($clearPopulationCache);

        PopupInfographic::saved($clearHomeData);
        PopupInfographic::deleted($clearHomeData);

        PublicFacility::saved(function () {
            Cache::forget('office_facility_coords');
            Cache::forget('map_facilities');
        });
        PublicFacility::deleted(function () {
            Cache::forget('office_facility_coords');
            Cache::forget('map_facilities');
        });

        Setting::saved(function () {
            Cache::forget('site_settings_all');
        });
        Setting::deleted(function () {
            Cache::forget('site_settings_all');
        });

        Service::saved(function () {
            Cache::forget('services_list');
        });
        Service::deleted(function () {
            Cache::forget('services_list');
        });
        ServiceRequirement::saved(function () {
            Cache::forget('services_list');
        });
        ServiceRequirement::deleted(function () {
            Cache::forget('services_list');
        });

        VillagePotential::saved(function () {
            Cache::forget('potentials_list');
        });
        VillagePotential::deleted(function () {
            Cache::forget('potentials_list');
        });

        try {
            // Cache all site settings permanently until Setting model is mutated
            $settings = Cache::rememberForever('site_settings_all', function () {
                try {
                    $raw = Setting::pluck('value', 'key')->all();
                    foreach ($raw as $key => $value) {
                        if (is_string($value) && str_starts_with($value, '[') && str_ends_with($value, ']')) {
                            $decoded = json_decode($value, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $raw[$key] = $decoded;
                            }
                        }
                    }

                    return $raw;
                } catch (\Throwable $e) {
                    return [];
                }
            });

            if (! empty($settings)) {
                View::share('site_settings', Arr::except($settings, ['tte_default_signer_nik']));

                if (isset($settings['village_name']) && ! empty($settings['village_name'])) {
                    $slug = Str::slug($settings['village_name']);
                    config(['backup.backup.name' => $slug]);
                    config(['backup.backup.destination.filename_prefix' => $slug.'-']);

                    Event::listen(CommandStarting::class, function ($event) use ($slug) {
                        try {
                            if ($event->command === 'backup:run' && $event->input->hasParameterOption('--filename')) {
                                $current = $event->input->getParameterOption('--filename');
                                if ($current && ! str_starts_with($current, $slug)) {
                                    $event->input->setOption('filename', $slug.'-'.$current);
                                }
                            }
                        } catch (\Throwable $e) {
                        }
                    });

                    $monitor = config('backup.monitor_backups');
                    if (is_array($monitor) && isset($monitor[0])) {
                        $monitor[0]['name'] = $slug;
                        config(['backup.monitor_backups' => $monitor]);
                    }
                }
            }

            // Muat statistik pengunjung dan koordinat kantor desa hanya saat layout publik dirender
            // agar request /admin, /livewire, /api, dan artisan CLI tidak terbebani query tambahan.
            View::composer('layouts.app', function ($view): void {
                $visitorStats = Cache::remember('visitor_stats_summary', 300, function () {
                    try {
                        $todayStr = now()->toDateString();
                        $yesterdayStr = now()->subDay()->toDateString();

                        $today = VisitorLog::where('visit_date', $todayStr)
                            ->distinct('ip_hash')
                            ->count('ip_hash');

                        $yesterday = VisitorLog::where('visit_date', $yesterdayStr)
                            ->distinct('ip_hash')
                            ->count('ip_hash');

                        $total = VisitorLog::distinct('ip_hash')
                            ->count('ip_hash');

                        return [
                            'today' => $today,
                            'yesterday' => $yesterday,
                            'total' => $total,
                        ];
                    } catch (\Throwable $e) {
                        return ['today' => 0, 'yesterday' => 0, 'total' => 0];
                    }
                });

                $officeFacility = Cache::remember('office_facility_coords', 86400, function () {
                    try {
                        return PublicFacility::select(['latitude', 'longitude'])
                            ->where(function ($q) {
                                $q->where('type', 'like', '%kantor%')
                                    ->orWhere('name', 'like', '%kantor%');
                            })
                            ->whereNotNull('latitude')
                            ->whereNotNull('longitude')
                            ->first();
                    } catch (\Throwable $e) {
                        return null;
                    }
                });

                $view->with([
                    'visitor_stats' => $visitorStats,
                    'office_facility' => $officeFacility,
                ]);
            });
        } catch (\Throwable $e) {
            // Database not ready or migrations not run yet, safe to ignore during boot
        }

        // Globally configure all Select components to use custom/searchable dropdown (Choices.js) instead of native
        Select::configureUsing(function (Select $select): void {
            $select->native(false);
        });
    }
}
