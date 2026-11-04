<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TelemetryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_503_when_monitoring_api_key_is_not_configured(): void
    {
        config(['services.monitoring.token' => null]);

        $response = $this->getJson('/api/v1/internal/telemetry');

        $response->assertStatus(503)
            ->assertJson([
                'status' => 'error',
                'message' => 'Monitoring API key is not configured on this server.',
            ]);
    }

    public function test_it_returns_401_when_token_is_missing(): void
    {
        config(['services.monitoring.token' => 'valid-secret-key-123']);

        $response = $this->getJson('/api/v1/internal/telemetry');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid monitoring token.',
            ]);
    }

    public function test_it_returns_401_when_token_is_invalid(): void
    {
        config(['services.monitoring.token' => 'valid-secret-key-123']);

        $response = $this->withToken('wrong-key-456')
            ->getJson('/api/v1/internal/telemetry');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid monitoring token.',
            ]);
    }

    public function test_it_returns_full_telemetry_payload_with_valid_bearer_token(): void
    {
        config(['services.monitoring.token' => 'valid-secret-key-123']);

        Setting::updateOrCreate(['key' => 'village_name'], ['value' => 'Desa Tompobulu']);

        $category = Category::create([
            'name' => 'Kabar Desa',
            'slug' => 'kabar-desa',
        ]);

        Post::create([
            'category_id' => $category->id,
            'title' => 'Pembangunan Saluran Irigasi Selesai',
            'slug' => 'pembangunan-saluran-irigasi-selesai',
            'content' => 'Pembangunan saluran air telah selesai dikerjakan.',
            'published_at' => now(),
        ]);

        Complaint::create([
            'name' => 'Warga 1',
            'phone' => '081234567890',
            'title' => 'Lampu Jalan Padam',
            'content' => 'Lampu jalan di dusun barat padam sejak kemarin.',
        ]);

        Cache::forever('telemetry_last_cron_at', now()->toIso8601String());

        $response = $this->withToken('valid-secret-key-123')
            ->getJson('/api/v1/internal/telemetry');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'app' => [
                    'name',
                    'village_slug',
                    'cms_version',
                    'laravel_version',
                    'php_version',
                    'environment',
                    'debug_mode',
                    'maintenance_mode',
                    'timezone',
                ],
                'backup' => [
                    'latest_backup_at',
                    'latest_backup_size_mb',
                    'storage_status',
                ],
                'content_activity' => [
                    'total_posts',
                    'posts_count_30d',
                    'latest_post_at',
                    'total_announcements',
                    'latest_announcement_at',
                ],
                'services_summary' => [
                    'pending_complaints',
                    'pending_service_requests',
                    'total_citizens',
                    'total_families',
                ],
                'system_health' => [
                    'database_connection',
                    'storage_writable',
                    'last_cron_executed_at',
                ],
            ]);

        $this->assertEquals('success', $response->json('status'));
        $this->assertEquals('Desa Tompobulu', $response->json('app.name'));
        $this->assertEquals(1, $response->json('content_activity.total_posts'));
        $this->assertEquals(1, $response->json('content_activity.posts_count_30d'));
        $this->assertEquals(1, $response->json('services_summary.pending_complaints'));
        $this->assertTrue($response->json('system_health.database_connection'));
        $this->assertTrue($response->json('system_health.storage_writable'));
        $this->assertNotNull($response->json('system_health.last_cron_executed_at'));
    }

    public function test_it_authenticates_via_x_monitoring_key_header(): void
    {
        config(['services.monitoring.token' => 'custom-header-key']);

        $response = $this->withHeader('X-Monitoring-Key', 'custom-header-key')
            ->getJson('/api/v1/internal/telemetry');

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);
    }
}
