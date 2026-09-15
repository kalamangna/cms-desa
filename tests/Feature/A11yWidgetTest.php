<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class A11yWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_a11y_widget_is_rendered_by_default_and_userway_is_absent(): void
    {
        View::share('site_settings', []);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('cdn.jsdelivr.net/gh/kalamangna/a11y-sinjaikab', false);
        $response->assertDontSee('cdn.userway.org', false);
        $response->assertDontSee('loadUserWay', false);
    }

    public function test_a11y_widget_can_be_disabled_via_settings(): void
    {
        Setting::create([
            'key' => 'enable_a11y_widget',
            'value' => '0',
        ]);
        View::share('site_settings', Setting::pluck('value', 'key')->all());

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('cdn.jsdelivr.net/gh/kalamangna/a11y-sinjaikab', false);
    }

    public function test_a11y_widget_uses_custom_position_and_default_color(): void
    {
        Setting::create([
            'key' => 'enable_a11y_widget',
            'value' => '1',
        ]);
        Setting::create([
            'key' => 'a11y_widget_position',
            'value' => 'bottom-right',
        ]);
        Setting::create([
            'key' => 'primary_color',
            'value' => '#0ea5e9',
        ]);
        View::share('site_settings', Setting::pluck('value', 'key')->all());

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('data-position="bottom-right"', false);
        $response->assertDontSee('data-color=', false);
    }
}
