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
}
