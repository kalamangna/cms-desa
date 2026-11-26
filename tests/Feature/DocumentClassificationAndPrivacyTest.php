<?php

namespace Tests\Feature;

use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DocumentClassificationAndPrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_documents_can_be_filtered_by_category_classification_year_and_search(): void
    {
        Document::create([
            'title' => 'Peraturan Desa tentang APBDes 2026',
            'file' => 'documents/perdes-apbdes-2026.pdf',
            'description' => 'Anggaran Pendapatan dan Belanja Desa tahun anggaran 2026.',
            'category' => 'perdes',
            'information_classification' => 'berkala',
            'document_number' => '03 Tahun 2026',
            'year' => 2026,
            'published_date' => '2026-01-15',
        ]);

        Document::create([
            'title' => 'Keputusan Kepala Desa tentang Satgas Bencana',
            'file' => 'documents/sk-satgas-2025.pdf',
            'description' => 'Penetapan tim tanggap darurat bencana desa.',
            'category' => 'sk_kades',
            'information_classification' => 'serta_merta',
            'document_number' => '12 Tahun 2025',
            'year' => 2025,
            'published_date' => '2025-11-10',
        ]);

        $byCategory = $this->get('/dokumen?category=perdes');
        $byCategory->assertStatus(200);
        $byCategory->assertSee('Peraturan Desa tentang APBDes 2026');
        $byCategory->assertSee('Peraturan Desa');
        $byCategory->assertSee('Informasi Berkala');
        $byCategory->assertSee('Nomor 03 Tahun 2026');
        $byCategory->assertDontSee('Keputusan Kepala Desa tentang Satgas Bencana');

        $byClassification = $this->get('/dokumen?classification=serta_merta');
        $byClassification->assertStatus(200);
        $byClassification->assertSee('Keputusan Kepala Desa tentang Satgas Bencana');
        $byClassification->assertDontSee('Peraturan Desa tentang APBDes 2026');

        $byYear = $this->get('/dokumen?year=2025');
        $byYear->assertStatus(200);
        $byYear->assertSee('Keputusan Kepala Desa tentang Satgas Bencana');
        $byYear->assertDontSee('Peraturan Desa tentang APBDes 2026');

        $byNumber = $this->get('/dokumen?search=03+Tahun+2026');
        $byNumber->assertStatus(200);
        $byNumber->assertSee('Peraturan Desa tentang APBDes 2026');
        $byNumber->assertDontSee('Keputusan Kepala Desa tentang Satgas Bencana');
    }

    public function test_documents_filter_ignores_invalid_parameters_safely(): void
    {
        Document::create([
            'title' => 'Peraturan Kepala Desa tentang Tata Tertib',
            'file' => 'documents/perkades-tatib.pdf',
            'category' => 'perkades',
            'information_classification' => 'setiap_saat',
            'year' => 2026,
        ]);

        $response = $this->get('/dokumen?category=invalid_cat&classification=invalid_class&year=9999');
        $response->assertStatus(200);
        $response->assertSee('Peraturan Kepala Desa tentang Tata Tertib');
    }

    public function test_privacy_policy_page_is_accessible_and_linked_in_footer(): void
    {
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee(route('privacy-policy'));
        $homeResponse->assertSee('Kebijakan Privasi');

        $privacyResponse = $this->get('/kebijakan-privasi');
        $privacyResponse->assertStatus(200);
        $privacyResponse->assertSee('Komitmen Perlindungan Data Pribadi');
        $privacyResponse->assertSee('Larangan Publikasi Data Sensitif');
    }

    public function test_sitemap_includes_privacy_policy_page(): void
    {
        Cache::forget('sitemap_xml_content');

        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertSee(url('/kebijakan-privasi'), false);
    }
}
