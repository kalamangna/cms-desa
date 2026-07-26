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
        if (config('app.env') === 'production' || str_starts_with(config('app.url', ''), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Paginator::useTailwind();

        // Register Google Drive Storage Driver
        try {
            \Illuminate\Support\Facades\Storage::extend('google', function ($app, $config) {
                $options = [];
                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $folderId = $config['folder'] ?? null;
                if (!empty($folderId)) {
                    $options['sharedFolderId'] = $folderId;
                }

                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service    = new \Google\Service\Drive($client);
                $rawAdapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, null, $options);
                $adapter    = new \App\Services\GoogleDriveAdapterWrapper($rawAdapter);
                $driver     = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        } catch (\Throwable $e) {}

        // Register custom Livewire backup list records component to prevent polling expiration
        \Livewire\Livewire::component('custom-backup-destination-list-records', \App\Filament\Components\CustomBackupDestinationListRecords::class);

        // Audit Log Listeners for Auth
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            try {
                \App\Models\AuditLog::create([
                    'user_id' => $event->user?->id,
                    'user_name' => $event->user?->name ?? 'Sistem',
                    'event' => 'login',
                    'description' => "Pengguna {$event->user?->name} berhasil masuk (login) ke sistem",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Throwable $e) {}
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
            try {
                if ($event->user) {
                    \App\Models\AuditLog::create([
                        'user_id' => $event->user->id,
                        'user_name' => $event->user->name,
                        'event' => 'logout',
                        'description' => "Pengguna {$event->user->name} keluar (logout) dari sistem",
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
            } catch (\Throwable $e) {}
        });

        // 1. Radar Keamanan Autentikasi (Logins) - Telegram Notification
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            try {
                $ip      = request()->ip();
                $ua      = request()->userAgent() ?? '-';
                $browser = strlen($ua) > 60 ? substr($ua, 0, 60) . '…' : $ua;
                $roles   = $event->user->getRoleNames()->implode(', ') ?: 'Tanpa role';
                $msg     = "👤 {$event->user->name}\n"
                         . "🔑 {$event->user->username}\n"
                         . "🎭 {$roles}\n"
                         . "🌐 {$ip}\n"
                         . "🖥 <code>{$browser}</code>";
                \Illuminate\Support\Facades\Notification::route('telegram', 'system')->notify(new \App\Notifications\SystemMonitorNotification('LOGIN BERHASIL', $msg, 'success'));
            } catch (\Throwable $e) {}
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Failed::class, function ($event) {
            try {
                $ip      = request()->ip();
                $ua      = request()->userAgent() ?? '-';
                $browser = strlen($ua) > 60 ? substr($ua, 0, 60) . '…' : $ua;
                $username = $event->credentials['username'] ?? 'tidak diketahui';
                $msg     = "🔑 {$username}\n"
                         . "🌐 {$ip}\n"
                         . "🖥 <code>{$browser}</code>";
                \Illuminate\Support\Facades\Notification::route('telegram', 'system')->notify(new \App\Notifications\SystemMonitorNotification('LOGIN GAGAL', $msg, 'danger'));
            } catch (\Throwable $e) {}
        });

        // 2. Pengawasan Hak Akses (User Management)
        \App\Models\User::created(function (\App\Models\User $user) {
            try {
                $roles = $user->getRoleNames()->implode(', ') ?: 'Tanpa role';
                $msg   = "👤 {$user->name}\n"
                       . "🔑 {$user->username}\n"
                       . "🎭 {$roles}";
                \Illuminate\Support\Facades\Notification::route('telegram', 'system')->notify(new \App\Notifications\SystemMonitorNotification('AKUN ADMIN DIBUAT', $msg, 'warning'));
            } catch (\Throwable $e) {}
        });

        \App\Models\User::deleted(function (\App\Models\User $user) {
            try {
                $roles = $user->getRoleNames()->implode(', ') ?: 'Tanpa role';
                $msg   = "👤 {$user->name}\n"
                       . "🔑 {$user->username}\n"
                       . "🎭 {$roles}";
                \Illuminate\Support\Facades\Notification::route('telegram', 'system')->notify(new \App\Notifications\SystemMonitorNotification('AKUN ADMIN DIHAPUS', $msg, 'danger'));
            } catch (\Throwable $e) {}
        });

        // 3. Perubahan Pengaturan Krusial
        \App\Models\Setting::updated(function (\App\Models\Setting $setting) {
            try {
                if (in_array($setting->key, ['sejarah_desa', 'visi_misi', 'peta_desa'])) return;

                $oldRaw = $setting->getOriginal('value') ?? '-';
                $newRaw = $setting->value ?? '-';
                // Potong jika terlalu panjang (misal JSON/teks panjang)
                $old    = strlen($oldRaw) > 80 ? substr($oldRaw, 0, 80) . '…' : $oldRaw;
                $new    = strlen($newRaw) > 80 ? substr($newRaw, 0, 80) . '…' : $newRaw;

                $msg = "⚙️ <code>{$setting->key}</code>\n"
                     . "📤 Lama: <code>{$old}</code>\n"
                     . "📥 Baru: <code>{$new}</code>";
                \Illuminate\Support\Facades\Notification::route('telegram', 'system')->notify(new \App\Notifications\SystemMonitorNotification('PENGATURAN DIUBAH', $msg, 'info'));
            } catch (\Throwable $e) {}
        });

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
            Cache::forget('home_total_rt');
            Cache::forget('home_total_rw');
            Cache::forget('home_total_keluarga');
            Cache::forget('home_total_penduduk_real');
            Cache::forget('home_job_stats');
            Cache::forget('home_edu_stats');
            Cache::forget('home_laki_laki_count');
            Cache::forget('home_perempuan_count');
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
        \App\Models\Citizen::saved($clearHomeCache);
        \App\Models\Citizen::deleted($clearHomeCache);
        \App\Models\Family::saved($clearHomeCache);
        \App\Models\Family::deleted($clearHomeCache);

        try {
            if (Schema::hasTable('settings')) {
                $settings = Setting::pluck('value', 'key')->all();
                foreach ($settings as $key => $value) {
                    if (is_string($value) && str_starts_with($value, '[') && str_ends_with($value, ']')) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $settings[$key] = $decoded;
                        }
                    }
                }
                View::share('site_settings', $settings);

                if (isset($settings['village_name']) && !empty($settings['village_name'])) {
                    $slug = \Illuminate\Support\Str::slug($settings['village_name']);
                    config(['backup.backup.name' => $slug]);
                    config(['backup.backup.destination.filename_prefix' => $slug . '-']);
                    
                    \Illuminate\Support\Facades\Event::listen(\Illuminate\Console\Events\CommandStarting::class, function ($event) use ($slug) {
                        if ($event->command === 'backup:run' && $event->input->hasParameterOption('--filename')) {
                            $current = $event->input->getParameterOption('--filename');
                            if ($current && !str_starts_with($current, $slug)) {
                                $event->input->setOption('filename', $slug . '-' . $current);
                            }
                        }
                    });

                    $monitor = config('backup.monitor_backups');
                    if (is_array($monitor) && isset($monitor[0])) {
                        $monitor[0]['name'] = $slug;
                        config(['backup.monitor_backups' => $monitor]);
                    }
                }
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
