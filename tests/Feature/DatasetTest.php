<?php

namespace Tests\Feature;

use App\Models\Dataset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatasetTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_dataset(): void
    {
        $dataset = Dataset::create([
            'title' => 'Data Penduduk 2024',
            'slug' => 'data-penduduk-2024',
            'year' => 2024,
            'source' => 'BPS',
        ]);

        $this->assertDatabaseHas('datasets', [
            'title' => 'Data Penduduk 2024',
            'year' => 2024,
        ]);
    }

    public function test_can_soft_delete_dataset(): void
    {
        $dataset = Dataset::create([
            'title' => 'Data Penduduk 2024',
            'slug' => 'data-penduduk-2024',
            'year' => 2024,
        ]);

        $dataset->delete();

        $this->assertSoftDeleted($dataset);
    }
}
