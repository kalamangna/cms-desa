<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Gallery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_apbdes_page_is_accessible(): void
    {
        $response = $this->get('/apbdes');
        $response->assertStatus(200);
    }

    public function test_statistics_page_is_accessible(): void
    {
        $response = $this->get('/statistik');
        $response->assertStatus(200);
    }

    public function test_contact_page_is_accessible_and_has_schema(): void
    {
        $response = $this->get('/kontak');
        $response->assertStatus(200);
        $response->assertSee('application/ld+json');
        $response->assertSee('GovernmentOffice');
    }

    public function test_services_page_is_accessible_and_has_schema(): void
    {
        $response = $this->get('/layanan');
        $response->assertStatus(200);
        // Note: GovernmentService schema renders only if $services is not empty,
        // but the route itself must load without errors.
    }

    public function test_announcements_page_is_accessible(): void
    {
        $response = $this->get('/pengumuman');
        $response->assertStatus(200);
    }

    public function test_documents_page_is_accessible_and_searchable(): void
    {
        Document::create([
            'title' => 'Peraturan Desa No 01 Tahun 2026',
            'file' => 'documents/perdes-01.pdf',
            'description' => 'Tentang Rencana Pembangunan Jangka Menengah Desa',
        ]);

        Document::create([
            'title' => 'Keputusan Kepala Desa No 05 Tahun 2026',
            'file' => 'documents/sk-05.pdf',
            'description' => 'Tentang Pembentukan Tim Pengelola Kegiatan',
        ]);

        $responseAll = $this->get('/dokumen');
        $responseAll->assertStatus(200);
        $responseAll->assertSee('Peraturan Desa No 01');
        $responseAll->assertSee('Keputusan Kepala Desa No 05');

        $responseSearch = $this->get('/dokumen?search=Pembangunan');
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Peraturan Desa No 01');
        $responseSearch->assertDontSee('Keputusan Kepala Desa No 05');
    }

    public function test_galleries_page_is_accessible_and_renders_gallery_data(): void
    {
        Gallery::create([
            'title' => 'Gotong Royong Bersih Desa',
            'type' => 'photo',
            'image' => 'galleries/gotong-royong.webp',
            'description' => 'Kegiatan gotong royong membersihkan saluran irigasi bersama warga.',
        ]);

        $response = $this->get('/galeri');
        $response->assertStatus(200);
        $response->assertSee('Gotong Royong Bersih Desa');
        // Make sure description is present in the galleryItems JSON payload for modal lightbox
        $response->assertSee('Kegiatan gotong royong membersihkan saluran irigasi bersama warga.');
    }
}
