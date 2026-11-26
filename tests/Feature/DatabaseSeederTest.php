<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\BudgetCategory;
use App\Models\BudgetRealization;
use App\Models\Category;
use App\Models\Document;
use App\Models\Official;
use App\Models\PopupInfographic;
use App\Models\Post;
use App\Models\Service;
use App\Models\Setting;
use App\Services\LetterNumberService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_prepares_clean_idempotent_baseline_without_dummy_content(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertTrue(Role::where('name', 'super_admin')->exists());
        $this->assertTrue(Role::where('name', 'admin_desa')->exists());

        $this->assertSame(10, Service::count());
        $this->assertSame(10, Service::where('is_active', true)->where('is_tte_enabled', true)->count());
        $this->assertSame(4, Category::count());
        $this->assertSame(3, BudgetCategory::count());

        $this->assertSame('0', Setting::where('key', 'tte_enabled')->value('value'));
        $this->assertSame('DESA', Setting::where('key', 'tte_village_code')->value('value'));
        $this->assertSame(
            LetterNumberService::DEFAULT_FORMAT,
            Setting::where('key', 'tte_letter_number_format')->value('value')
        );

        // Pastikan tidak ada konten dummy maupun sisa nama desa hardcoded
        $this->assertSame(0, Post::count());
        $this->assertSame(0, Announcement::count());
        $this->assertSame(0, Document::count());
        $this->assertSame(0, BudgetRealization::count());
        $this->assertSame(0, PopupInfographic::count());
        $this->assertSame(0, Official::count());
        $this->assertDatabaseMissing('settings', ['value' => 'Tompobulu']);
    }
}
