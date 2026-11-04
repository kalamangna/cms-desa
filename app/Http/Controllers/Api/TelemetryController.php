<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Citizen;
use App\Models\Complaint;
use App\Models\Family;
use App\Models\Post;
use App\Models\ServiceRequest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Backup\BackupDestination\BackupDestination;
use Throwable;

class TelemetryController extends Controller
{
    /**
     * Mengembalikan data status dan telemetri sistem untuk dasbor monitoring terpusat.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'timestamp' => now()->toIso8601String(),
            'app' => $this->getAppInfo(),
            'backup' => $this->getBackupInfo(),
            'content_activity' => $this->getContentActivity(),
            'services_summary' => $this->getServicesSummary(),
            'system_health' => $this->getSystemHealth(),
        ]);
    }

    /**
     * Informasi aplikasi, CMS, dan lingkungan server.
     */
    protected function getAppInfo(): array
    {
        $villageName = null;
        try {
            $villageName = Setting::where('key', 'village_name')->value('value');
        } catch (Throwable) {
            // Abaikan jika tabel settings belum siap
        }

        return [
            'name' => $villageName ?: config('app.name'),
            'village_slug' => config('backup.backup.name'),
            'cms_version' => config('app.version', '1.0.0'),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment(),
            'debug_mode' => (bool) config('app.debug'),
            'maintenance_mode' => app()->isDownForMaintenance(),
            'timezone' => config('app.timezone'),
        ];
    }

    /**
     * Status pencadangan (backup) lokal dan cloud storage.
     */
    protected function getBackupInfo(): array
    {
        $disks = config('backup.backup.destination.disks', ['local', 'google']);
        $backupName = config('backup.backup.name');
        $storageStatus = [];
        $latestBackupDate = null;
        $latestBackupSize = null;

        foreach ($disks as $disk) {
            try {
                $destination = BackupDestination::create($disk, $backupName);
                $isReachable = $destination->isReachable();
                $count = $isReachable ? $destination->backups()->count() : 0;
                $newest = $isReachable ? $destination->newestBackup() : null;

                $newestDate = $newest?->date();
                $newestSizeBytes = $newest?->sizeInBytes();

                $storageStatus[$disk] = [
                    'reachable' => $isReachable,
                    'count' => $count,
                    'newest_backup_at' => $newestDate ? Carbon::instance($newestDate)->toIso8601String() : null,
                    'newest_backup_size_mb' => $newestSizeBytes ? round($newestSizeBytes / (1024 * 1024), 2) : 0,
                ];

                if ($newestDate && (! $latestBackupDate || Carbon::instance($newestDate)->greaterThan($latestBackupDate))) {
                    $latestBackupDate = Carbon::instance($newestDate);
                    $latestBackupSize = $newestSizeBytes;
                }
            } catch (Throwable $e) {
                $storageStatus[$disk] = [
                    'reachable' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'latest_backup_at' => $latestBackupDate?->toIso8601String(),
            'latest_backup_size_mb' => $latestBackupSize ? round($latestBackupSize / (1024 * 1024), 2) : null,
            'storage_status' => $storageStatus,
        ];
    }

    /**
     * Aktivitas publikasi konten dan berita.
     */
    protected function getContentActivity(): array
    {
        try {
            $totalPosts = Post::count();
            $latestPost = Post::latest('published_at')->first(['published_at', 'created_at']);
            $latestPostAt = $latestPost?->published_at ?? $latestPost?->created_at;
            $postsCount30d = Post::where('published_at', '>=', now()->subDays(30))->count();

            $totalAnnouncements = Announcement::count();
            $latestAnnouncement = Announcement::latest('created_at')->first(['created_at']);

            return [
                'total_posts' => $totalPosts,
                'posts_count_30d' => $postsCount30d,
                'latest_post_at' => $latestPostAt ? Carbon::parse($latestPostAt)->toIso8601String() : null,
                'total_announcements' => $totalAnnouncements,
                'latest_announcement_at' => $latestAnnouncement?->created_at ? Carbon::parse($latestAnnouncement->created_at)->toIso8601String() : null,
            ];
        } catch (Throwable) {
            return [
                'total_posts' => 0,
                'posts_count_30d' => 0,
                'latest_post_at' => null,
                'total_announcements' => 0,
                'latest_announcement_at' => null,
            ];
        }
    }

    /**
     * Ringkasan layanan mandiri masyarakat dan agregat data penduduk.
     */
    protected function getServicesSummary(): array
    {
        try {
            $pendingComplaints = Complaint::where('status', 'Menunggu')->count();
            $pendingServiceRequests = ServiceRequest::where('status', 'Menunggu')->count();
            $totalCitizens = Citizen::count();
            $totalFamilies = Family::count();

            return [
                'pending_complaints' => $pendingComplaints,
                'pending_service_requests' => $pendingServiceRequests,
                'total_citizens' => $totalCitizens,
                'total_families' => $totalFamilies,
            ];
        } catch (Throwable) {
            return [
                'pending_complaints' => 0,
                'pending_service_requests' => 0,
                'total_citizens' => 0,
                'total_families' => 0,
            ];
        }
    }

    /**
     * Pemeriksaan kesehatan internal dan heartbeat background scheduler.
     */
    protected function getSystemHealth(): array
    {
        $dbConnected = false;
        try {
            DB::connection()->getPdo();
            $dbConnected = true;
        } catch (Throwable) {
            $dbConnected = false;
        }

        $storageWritable = false;
        try {
            $testFile = storage_path('framework/cache/.health_check_'.uniqid());
            if (@file_put_contents($testFile, 'ok') !== false) {
                @unlink($testFile);
                $storageWritable = true;
            }
        } catch (Throwable) {
            $storageWritable = false;
        }

        return [
            'database_connection' => $dbConnected,
            'storage_writable' => $storageWritable,
            'last_cron_executed_at' => Cache::get('telemetry_last_cron_at'),
        ];
    }
}
