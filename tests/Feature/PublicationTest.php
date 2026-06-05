<?php

namespace Tests\Feature;

use App\Models\Publication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_publication(): void
    {
        $publication = Publication::create([
            'title' => 'Desa Tompobulu Dalam Angka 2024',
            'slug' => 'desa-tompobulu-dalam-angka-2024',
            'type' => 'Desa Dalam Angka',
            'year' => 2024,
            'pdf_file' => 'test.pdf',
        ]);

        $this->assertDatabaseHas('publications', [
            'title' => 'Desa Tompobulu Dalam Angka 2024',
            'type' => 'Desa Dalam Angka',
        ]);
    }

    public function test_can_soft_delete_publication(): void
    {
        $publication = Publication::create([
            'title' => 'Desa Tompobulu Dalam Angka 2024',
            'slug' => 'desa-tompobulu-dalam-angka-2024',
            'type' => 'Desa Dalam Angka',
            'year' => 2024,
            'pdf_file' => 'test.pdf',
        ]);

        $publication->delete();

        $this->assertSoftDeleted($publication);
    }
}
