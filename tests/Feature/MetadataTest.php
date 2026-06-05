<?php

namespace Tests\Feature;

use App\Models\Dataset;
use App\Models\Metadata;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetadataTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_metadata_for_dataset(): void
    {
        $dataset = Dataset::create([
            'title' => 'Data Penduduk 2024',
            'slug' => 'data-penduduk-2024',
            'year' => 2024,
        ]);

        $metadata = Metadata::create([
            'dataset_id' => $dataset->id,
            'source' => 'BPS Maros',
            'definition' => 'Data penduduk berdasarkan hasil sensus.',
            'update_frequency' => 'Tahunan',
            'responsible_person' => 'Kepala Desa',
        ]);

        $this->assertDatabaseHas('metadata', [
            'dataset_id' => $dataset->id,
            'source' => 'BPS Maros',
        ]);
        
        $this->assertEquals($dataset->id, $metadata->dataset->id);
        $this->assertEquals($metadata->id, $dataset->fresh()->metadata->id);
    }
}
