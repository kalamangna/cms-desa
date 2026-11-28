<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\SettingChangeNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
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

    public function test_setting_change_notifier_batches_updates_and_masks_sensitive_keys(): void
    {
        Notification::fake();

        $village = Setting::create(['key' => 'village_name', 'value' => 'Lama']);
        $district = Setting::create(['key' => 'district_name', 'value' => 'Sinjai']);
        $signerNik = Setting::create(['key' => 'tte_default_signer_nik', 'value' => '7307010000000001']);
        $history = Setting::create(['key' => 'sejarah_desa', 'value' => 'Sejarah lama']);

        $notifier = app(SettingChangeNotifier::class);
        $notifier->reset();

        $village->value = 'Alenangka';
        $district->value = 'Sinjai Selatan';
        $signerNik->value = '7307019999999999';
        $history->value = 'Sejarah baru';

        $notifier->record($village);
        $notifier->record($district);
        $notifier->record($signerNik);
        $notifier->record($history);

        $summary = $notifier->buildSummaryMessage('Operator Desa');
        $this->assertNotNull($summary);
        $this->assertStringContainsString('Jumlah diubah: <b>3</b>', $summary);
        $this->assertStringContainsString('village_name', $summary);
        $this->assertStringContainsString('district_name', $summary);
        $this->assertStringContainsString('tte_default_signer_nik', $summary);
        $this->assertStringContainsString('[TERSEMBUNYI]', $summary);
        $this->assertStringNotContainsString('7307010000000001', $summary);
        $this->assertStringNotContainsString('7307019999999999', $summary);
        $this->assertStringNotContainsString('sejarah_desa', $summary);

        $this->assertTrue($notifier->flush('Operator Desa'));
        $this->assertFalse($notifier->flush('Operator Desa'));

        // Perubahan hanya pada key non-krusial tidak mengirim notifikasi
        $notifier->record($history);
        $this->assertNull($notifier->buildSummaryMessage('Operator Desa'));
        $this->assertFalse($notifier->flush('Operator Desa'));
    }
}
