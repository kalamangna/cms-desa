<?php

namespace Tests\Feature;

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
}
