<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta property="og:title"', false);
    }
}
