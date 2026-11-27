<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class SEOTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_xml_is_accessible(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $this->assertStringContainsString('text/xml', $response->headers->get('Content-Type'));
        $response->assertSee('<urlset', false);
    }

    public function test_robots_txt_is_accessible(): void
    {
        $response = $this->get('/robots.txt');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('User-agent: *');
        $response->assertSee('Sitemap:');
    }

    public function test_homepage_has_meta_tags(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('<title>Pemerintah Desa</title>', false);
        $response->assertSee('<meta name="description" content="Website resmi Pemerintah Desa. Akses layanan mandiri, APBDes, statistik, dan berita desa.">', false);
        $response->assertSee('<meta property="og:title" content="Pemerintah Desa">', false);
        $response->assertDontSee('&amp;amp;', false);
        $response->assertDontSee('Desa  |', false);
        $response->assertDontSee('Kec. ,', false);
    }

    public function test_homepage_meta_tags_normalize_region_names_when_settings_configured(): void
    {
        Setting::updateOrCreate(['key' => 'village_name'], ['value' => 'Alenangka']);
        Setting::updateOrCreate(['key' => 'district_name'], ['value' => 'Sinjai Selatan']);
        Setting::updateOrCreate(['key' => 'regency_name'], ['value' => 'Kabupaten Sinjai']);
        View::share('site_settings', Setting::pluck('value', 'key')->toArray());

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('<title>Desa Alenangka | Kabupaten Sinjai</title>', false);
        $response->assertSee('<meta name="description" content="Website resmi Desa Alenangka, Kecamatan Sinjai Selatan, Kabupaten Sinjai. Akses layanan mandiri, APBDes, statistik, dan berita desa.">', false);
        $response->assertDontSee('Kabupaten Kabupaten', false);
    }

    public function test_secondary_public_pages_normalize_village_title_and_description(): void
    {
        $emptyResponse = $this->get('/profil');
        $emptyResponse->assertStatus(200);
        $emptyResponse->assertSee('<title>Profil Desa | Pemerintah Desa</title>', false);
        $emptyResponse->assertDontSee('| Desa</title>', false);
        $emptyResponse->assertDontSee('pemerintahan Pemerintah', false);
        $emptyResponse->assertDontSee('Pemerintah Desa .', false);

        Setting::updateOrCreate(['key' => 'village_name'], ['value' => 'Desa Alenangka']);
        View::share('site_settings', Setting::pluck('value', 'key')->toArray());

        $filledResponse = $this->get('/profil');
        $filledResponse->assertStatus(200);
        $filledResponse->assertSee('<title>Profil Desa | Desa Alenangka</title>', false);
        $filledResponse->assertSee('<meta name="description" content="Profil lengkap sejarah pembentukan, letak geografis, visi dan misi Pemerintah Desa Alenangka.">', false);
        $filledResponse->assertSee('<meta property="og:title" content="Profil Desa | Desa Alenangka">', false);
    }
}
