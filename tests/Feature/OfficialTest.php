<?php

namespace Tests\Feature;

use App\Models\Official;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficialTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_official(): void
    {
        $official = Official::create([
            'name' => 'Budi Santoso',
            'position' => 'Kepala Desa',
            'period_start' => '2024-01-01',
        ]);

        $this->assertDatabaseHas('officials', [
            'name' => 'Budi Santoso',
            'position' => 'Kepala Desa',
        ]);
    }

    public function test_can_soft_delete_official(): void
    {
        $official = Official::create([
            'name' => 'Budi Santoso',
            'position' => 'Kepala Desa',
            'period_start' => '2024-01-01',
        ]);

        $official->delete();

        $this->assertSoftDeleted($official);
    }
}
