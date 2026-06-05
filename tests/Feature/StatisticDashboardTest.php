<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistic_dashboard_page_is_accessible(): void
    {
        $response = $this->get('/statistik');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Statistik Desa');
    }
}
