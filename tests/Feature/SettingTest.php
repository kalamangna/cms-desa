<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_store_setting(): void
    {
        $setting = Setting::create([
            'key' => 'village_name',
            'value' => 'Tompobulu',
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'village_name',
            'value' => 'Tompobulu',
        ]);
    }

    public function test_can_update_setting(): void
    {
        $setting = Setting::create([
            'key' => 'village_name',
            'value' => 'Tompobulu',
        ]);

        $setting->update(['value' => 'Tompobulu Baru']);

        $this->assertEquals('Tompobulu Baru', $setting->fresh()->value);
    }
}
