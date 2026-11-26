<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceRequest;
use App\Services\ServiceStandardizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceStandardizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_standardization_normalizes_aliases_preserves_requests_and_local_services(): void
    {
        $domisili = Service::create([
            'title' => 'Keterangan Domisili',
            'description' => 'Deskripsi kustom desa untuk domisili.',
            'letter_template' => '<p>Template kustom desa</p>',
            'is_tte_enabled' => false,
        ]);

        $domisili->requirementItems()->create([
            'name' => 'Berkas Kustom Desa',
            'description' => 'Persyaratan khusus',
            'is_required' => true,
            'sort_order' => 1,
        ]);

        $request = ServiceRequest::create([
            'nik' => '7307011234560001',
            'name' => 'Warga Uji',
            'phone' => '081234567890',
            'service_id' => $domisili->id,
        ]);

        Service::create([
            'title' => 'Keterangan Usaha',
            'description' => 'Usaha lama',
        ]);

        Service::create([
            'title' => 'Pengantar Perubahan KK',
            'description' => 'KK lama',
        ]);

        Service::create([
            'title' => 'Surat Keterangan Kurang Mampu',
            'description' => 'SKTM lama',
        ]);

        Service::create([
            'title' => 'Pengantar Nikah',
            'description' => 'Nikah lama',
        ]);

        Service::create([
            'title' => 'Surat Izin Keramaian',
            'description' => 'Keramaian lama',
        ]);

        $ambulans = Service::create([
            'title' => 'Layanan Mobil Ambulans',
            'description' => 'Layanan darurat desa.',
        ]);

        $kapal = Service::create([
            'title' => 'Surat Keterangan Kapal',
            'description' => 'Layanan khusus desa pesisir.',
        ]);

        $result = app(ServiceStandardizationService::class)->standardize();

        $this->assertSame(6, $result['updated']);
        $this->assertSame(4, $result['created']);
        $this->assertSame(2, $result['preserved_custom']);
        $this->assertSame(12, Service::count());

        $domisili->refresh();
        $this->assertSame('Surat Keterangan Domisili', $domisili->title);
        $this->assertSame('Kependudukan', $domisili->category);
        $this->assertSame(1, $domisili->sort_order);
        $this->assertTrue($domisili->is_tte_enabled);
        $this->assertTrue($domisili->is_active);
        // Kustomisasi desa tetap terjaga
        $this->assertSame('Deskripsi kustom desa untuk domisili.', $domisili->description);
        $this->assertSame('<p>Template kustom desa</p>', $domisili->letter_template);
        $this->assertSame(1, $domisili->requirementItems()->count());
        $this->assertSame('Berkas Kustom Desa', $domisili->requirementItems()->first()->name);

        // Riwayat permohonan tetap terhubung ke service_id yang sama
        $request->refresh();
        $this->assertSame($domisili->id, $request->service_id);

        // Layanan khusus lokal tetap dipertahankan
        $ambulans->refresh();
        $kapal->refresh();
        $this->assertSame('Layanan Lainnya', $ambulans->category);
        $this->assertSame('Layanan Lainnya', $kapal->category);
        $this->assertGreaterThanOrEqual(100, $ambulans->sort_order);

        // Idempotent saat dijalankan ulang
        $secondRun = app(ServiceStandardizationService::class)->standardize();
        $this->assertSame(10, $secondRun['updated']);
        $this->assertSame(0, $secondRun['created']);
        $this->assertSame(12, Service::count());
    }

    public function test_artisan_command_standardizes_empty_database(): void
    {
        $this->artisan('app:standardize-services')
            ->assertSuccessful();

        $this->assertSame(10, Service::count());
        $this->assertSame(10, Service::where('is_tte_enabled', true)->count());
        $this->assertSame(10, Service::where('is_active', true)->count());
    }

    public function test_inactive_services_are_hidden_from_layanan_and_rejected_on_submission(): void
    {
        $active = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'category' => 'Kependudukan',
            'sort_order' => 1,
            'description' => 'Layanan aktif.',
            'is_active' => true,
        ]);

        $inactive = Service::create([
            'title' => 'Layanan Nonaktif',
            'category' => 'Layanan Lainnya',
            'sort_order' => 2,
            'description' => 'Layanan disembunyikan.',
            'is_active' => false,
        ]);

        $page = $this->get(route('layanan'));
        $page->assertOk()
            ->assertSee($active->title)
            ->assertDontSee($inactive->title);

        $submit = $this->post(route('service-requests.store'), [
            'nik' => '7307011234560001',
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'service_id' => $inactive->id,
            '_hp_website' => '',
        ]);

        $submit->assertSessionHasErrors('service_id');
        $this->assertDatabaseCount('service_requests', 0);
    }
}
