<?php

namespace Tests\Feature;

use App\Filament\Resources\AuditLogs\AuditLogResource;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_creation_logs_audit_with_filtered_attributes_and_truncated_content(): void
    {
        $category = Category::create([
            'name' => 'Berita Desa',
            'slug' => 'berita-desa',
        ]);

        $longContent = '<p>'.str_repeat('Ini adalah teks konten berita yang sangat panjang untuk pengujian sistem audit log. ', 10).'</p>';

        $post = Post::create([
            'category_id' => $category->id,
            'title' => 'Pengumuman Penting Desa',
            'content' => $longContent,
            'published_at' => now(),
        ]);

        $log = AuditLog::where('auditable_type', Post::class)
            ->where('auditable_id', (string) $post->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($log);
        $this->assertStringContainsString('Pengumuman Penting Desa', $log->description);

        // Pastikan atribut waktu internal disaring
        $this->assertArrayNotHasKey('created_at', $log->new_values);
        $this->assertArrayNotHasKey('updated_at', $log->new_values);

        // Pastikan konten HTML dibersihkan dan dipotong tidak melebihi batas ringkasan
        $this->assertLessThanOrEqual(130, strlen($log->new_values['content']));
        $this->assertStringNotContainsString('<p>', $log->new_values['content']);
    }

    public function test_user_internal_token_update_does_not_trigger_audit_log(): void
    {
        $user = User::factory()->create([
            'username' => 'operator_desa',
            'remember_token' => null,
        ]);

        $countBefore = AuditLog::count();

        // Update hanya remember_token
        $user->update([
            'remember_token' => 'test_remember_token_123',
        ]);

        $countAfter = AuditLog::count();

        // Tidak boleh ada penambahan audit log untuk perubahan remember_token saja
        $this->assertEquals($countBefore, $countAfter);
    }

    public function test_audit_log_resource_parses_user_agent_cleanly(): void
    {
        $chromeMac = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
        $firefoxWin = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/119.0';
        $safariIos = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';

        $this->assertEquals('Chrome (macOS)', AuditLogResource::parseUserAgent($chromeMac));
        $this->assertEquals('Firefox (Windows)', AuditLogResource::parseUserAgent($firefoxWin));
        $this->assertEquals('Safari (iOS)', AuditLogResource::parseUserAgent($safariIos));
        $this->assertEquals('-', AuditLogResource::parseUserAgent(null));
    }

    public function test_audit_log_resource_renders_changes_html_table(): void
    {
        $log = AuditLog::create([
            'user_name' => 'Admin Desa',
            'event' => 'updated',
            'auditable_type' => Post::class,
            'auditable_id' => '1',
            'description' => 'Mengubah data Post: Judul Berita',
            'old_values' => ['title' => 'Judul Lama', 'is_active' => false],
            'new_values' => ['title' => 'Judul Baru', 'is_active' => true],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
        ]);

        $html = AuditLogResource::renderChangesHtml($log);

        $this->assertStringContainsString('Judul', $html);
        $this->assertStringContainsString('Judul Lama', $html);
        $this->assertStringContainsString('Judul Baru', $html);
        $this->assertStringContainsString('Ya', $html);
        $this->assertStringContainsString('Tidak', $html);
    }
}
