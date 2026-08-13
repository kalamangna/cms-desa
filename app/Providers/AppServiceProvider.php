<?php

namespace App\Providers;

use App\Filament\Components\CustomBackupDestinationListRecords;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\BudgetRealization;
use App\Models\Citizen;
use App\Models\Dusun;
use App\Models\Family;
use App\Models\Gallery;
use App\Models\Official;
use App\Models\Post;
use App\Models\Publication;
use App\Models\Setting;
use App\Models\StatisticData;
use App\Models\User;
use App\Models\VisitorLog;
use App\Notifications\SystemMonitorNotification;
use App\Services\GoogleDriveAdapterWrapper;
use Filament\Forms\Components\Select;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use League\Flysystem\Filesystem;
use Livewire\Livewire;
use Masbug\Flysystem\GoogleDriveAdapter;

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
            URL::forceScheme('https');
        }

        Paginator::useTailwind();

        // Register Google Drive Storage Driver
        try {
            Storage::extend('google', function ($app, $config) {
                $options = [];
                if (! empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $folderId = $config['folder'] ?? null;
                if (! empty($folderId)) {
                    $options['sharedFolderId'] = $folderId;
                }

                $client = new Client;
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new Drive($client);
                $rawAdapter = new GoogleDriveAdapter($service, null, $options);
                $adapter = new GoogleDriveAdapterWrapper($rawAdapter);
                $driver = new Filesystem($adapter);

                return new FilesystemAdapter($driver, $adapter);
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

        // 3. Perubahan Pengaturan Krusial
        Setting::updated(function (Setting $setting) {
            try {
                if (auth()->check() && auth()->user()->hasRole('super_admin')) {
                    return;
                }

                if (in_array($setting->key, ['sejarah_desa', 'visi_misi', 'peta_desa'])) {
                    return;
                }

                $oldRaw = $setting->getOriginal('value') ?? '-';
                $newRaw = $setting->value ?? '-';
                // Potong jika terlalu panjang (misal JSON/teks panjang)
                $old = strlen($oldRaw) > 80 ? substr($oldRaw, 0, 80).'…' : $oldRaw;
                $new = strlen($newRaw) > 80 ? substr($newRaw, 0, 80).'…' : $newRaw;

                $msg = "⚙️ <code>{$setting->key}</code>\n"
                     ."📤 Lama: <code>{$old}</code>\n"
                     ."📥 Baru: <code>{$new}</code>";
                Notification::route('telegram', 'system')->notify(new SystemMonitorNotification('PENGATURAN DIUBAH', $msg, 'info'));
            } catch (\Throwable $e) {
            }
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

        Post::saved($clearHomeCache);
        Post::deleted($clearHomeCache);
        Announcement::saved($clearHomeCache);
        Announcement::deleted($clearHomeCache);
        Official::saved($clearHomeCache);
        Official::deleted($clearHomeCache);
        StatisticData::saved($clearHomeCache);
        StatisticData::deleted($clearHomeCache);
        BudgetRealization::saved($clearHomeCache);
        BudgetRealization::deleted($clearHomeCache);
        Publication::saved($clearHomeCache);
        Publication::deleted($clearHomeCache);
        Gallery::saved($clearHomeCache);
        Gallery::deleted($clearHomeCache);
        Dusun::saved($clearHomeCache);
        Dusun::deleted($clearHomeCache);
        Citizen::saved($clearHomeCache);
        Citizen::deleted($clearHomeCache);
        Family::saved($clearHomeCache);
        Family::deleted($clearHomeCache);

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

                if (isset($settings['village_name']) && ! empty($settings['village_name'])) {
                    $slug = Str::slug($settings['village_name']);
                    config(['backup.backup.name' => $slug]);
                    config(['backup.backup.destination.filename_prefix' => $slug.'-']);

                    Event::listen(CommandStarting::class, function ($event) use ($slug) {
                        if ($event->command === 'backup:run' && $event->input->hasParameterOption('--filename')) {
                            $current = $event->input->getParameterOption('--filename');
                            if ($current && ! str_starts_with($current, $slug)) {
                                $event->input->setOption('filename', $slug.'-'.$current);
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
                });
                View::share('visitor_stats', $visitorStats);
            }
        } catch (\Throwable $e) {
            // Database not ready or migrations not run yet, safe to ignore during boot
        }

        // Globally configure all Select components to use custom/searchable dropdown (Choices.js) instead of native
        Select::configureUsing(function (Select $select): void {
            $select->native(false);
        });
    }
}
