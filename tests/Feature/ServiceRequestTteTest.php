<?php

namespace Tests\Feature;

use App\Exceptions\BsreException;
use App\Filament\Resources\ServiceRequests\Pages\EditServiceRequest;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\AuditLog;
use App\Models\Citizen;
use App\Models\Dusun;
use App\Models\LetterSequence;
use App\Models\Official;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Setting;
use App\Models\User;
use App\Providers\AppServiceProvider;
use App\Services\BsreService;
use App\Services\LetterDocumentService;
use App\Support\LetterTemplatePresets;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpClientRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServiceRequestTteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');

        Setting::updateOrCreate(['key' => 'village_name'], ['value' => 'Tompobulu']);
        Setting::updateOrCreate(['key' => 'district_name'], ['value' => 'Bulupoddo']);
        Setting::updateOrCreate(['key' => 'regency_name'], ['value' => 'Sinjai']);
        Setting::updateOrCreate(['key' => 'tte_village_code'], ['value' => 'TPB']);
    }

    public function test_public_submission_validates_and_stores_dynamic_form_fields(): void
    {
        $preset = LetterTemplatePresets::get('usaha');

        $service = Service::create([
            'title' => 'Surat Keterangan Usaha',
            'description' => 'Layanan SKU',
            'letter_code' => $preset['letter_code'],
            'letter_title' => $preset['letter_title'],
            'letter_template' => $preset['letter_template'],
            'form_fields' => $preset['form_fields'],
            'is_tte_enabled' => true,
        ]);

        // Gagal jika kolom dinamis wajib tidak diisi
        $failResponse = $this->post(route('service-requests.store'), [
            'nik' => '7307011234560001',
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'service_id' => $service->id,
            '_hp_website' => '',
        ]);

        $failResponse->assertSessionHasErrors('form_data.nama_usaha');
        $this->assertDatabaseCount('service_requests', 0);

        // Berhasil jika seluruh kolom dinamis wajib terisi
        $okResponse = $this->post(route('service-requests.store'), [
            'nik' => '7307011234560001',
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'service_id' => $service->id,
            'form_data' => [
                'nama_usaha' => 'Warung Berkah',
                'bidang_usaha' => 'Perdagangan',
                'alamat_usaha' => 'Dusun Mattirowalie',
                'keperluan' => 'Pengajuan kredit usaha',
            ],
            '_hp_website' => '',
        ]);

        $okResponse->assertRedirect(route('layanan'));
        $okResponse->assertSessionHas('success');

        $requestRecord = ServiceRequest::firstOrFail();
        $this->assertSame('Warung Berkah', $requestRecord->form_data['nama_usaha']);
    }

    public function test_draft_pdf_generation_pulls_citizen_data_and_increments_letter_number(): void
    {
        $dusun = Dusun::create(['name' => 'Mattirowalie', 'total_rt' => 2, 'total_rw' => 1]);
        Citizen::create([
            'nik' => '7307011234560002',
            'dusun_id' => $dusun->id,
            'name' => 'Siti Aminah',
            'place_of_birth' => 'Sinjai',
            'date_of_birth' => '1995-05-10',
            'gender' => 'Perempuan',
            'religion' => 'Islam',
            'job' => 'Wiraswasta',
            'address' => 'Jalan Poros Desa',
            'rt' => '001',
            'rw' => '002',
            'status' => 'Aktif',
        ]);

        $preset = LetterTemplatePresets::get('domisili');
        $service = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Domisili',
            'letter_code' => $preset['letter_code'],
            'letter_title' => $preset['letter_title'],
            'letter_template' => $preset['letter_template'],
            'form_fields' => $preset['form_fields'],
            'is_tte_enabled' => true,
        ]);

        $req1 = ServiceRequest::create([
            'nik' => '7307011234560002',
            'name' => 'Siti Aminah',
            'phone' => '081234567890',
            'service_id' => $service->id,
            'form_data' => ['keperluan' => 'Administrasi bank'],
        ]);

        $req2 = ServiceRequest::create([
            'nik' => '7307011234560099',
            'name' => 'Ahmad',
            'phone' => '081234567891',
            'service_id' => $service->id,
            'form_data' => ['keperluan' => 'Pendaftaran sekolah'],
        ]);

        $letterService = app(LetterDocumentService::class);
        $path1 = $letterService->generateDraftPdf($req1);
        $path2 = $letterService->generateDraftPdf($req2);

        $req1->refresh();
        $req2->refresh();

        Storage::disk('local')->assertExists($path1);
        Storage::disk('local')->assertExists($path2);
        $this->assertSame('Sinjai', $req1->letter_payload['place_of_birth']);
        $this->assertSame('Mattirowalie', $req1->letter_payload['dusun']);
        $this->assertSame(ServiceRequest::TTE_DRAFT, $req1->tte_status);
        $this->assertStringContainsString('/001/TPB/', $req1->letter_number);
        $this->assertStringContainsString('/002/TPB/', $req2->letter_number);
        $this->assertNotSame($req1->letter_number, $req2->letter_number);
    }

    public function test_bsre_signing_sends_expected_multipart_payload_and_never_stores_passphrase(): void
    {
        config([
            'bsre.mock' => false,
            'bsre.base_url' => 'https://tte.sinjaikab.go.id/bridge',
            'bsre.username' => 'desa_user',
            'bsre.password' => 'desa_secret',
        ]);

        $signedBinary = "%PDF-1.4\n%SignedByBSrE\n%%EOF";

        Http::fake([
            'https://tte.sinjaikab.go.id/bridge/api/sign/pdf' => Http::response($signedBinary, 200, [
                'Content-Type' => 'application/pdf',
            ]),
        ]);

        $user = User::factory()->create();
        $official = Official::create([
            'name' => 'H. Andi Muh. Rizal',
            'position' => 'Kepala Desa',
            'level' => 1,
            'order' => 1,
        ]);

        $preset = LetterTemplatePresets::get('pengantar');
        $service = Service::create([
            'title' => 'Surat Pengantar Umum',
            'description' => 'Pengantar',
            'letter_code' => $preset['letter_code'],
            'letter_title' => $preset['letter_title'],
            'letter_template' => $preset['letter_template'],
            'form_fields' => $preset['form_fields'],
            'is_tte_enabled' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'nik' => '7307011234560003',
            'name' => 'Rahmat',
            'phone' => '081234567892',
            'service_id' => $service->id,
            'form_data' => ['keperluan' => 'Pembuatan SKCK'],
        ]);

        $secretPassphrase = 'RahasiaSuperAman!2026';
        $signedRequest = app(LetterDocumentService::class)->signWithBsre(
            $serviceRequest,
            $official,
            '7307019999880001',
            $secretPassphrase,
            $user
        );

        Http::assertSent(function (HttpClientRequest $request): bool {
            $body = $request->body();

            return $request->url() === 'https://tte.sinjaikab.go.id/bridge/api/sign/pdf'
                && $request->hasHeader('Authorization')
                && str_contains($body, '7307019999880001')
                && str_contains($body, 'invisible');
        });

        $draftHtml = app(LetterDocumentService::class)->renderHtml($signedRequest, false);
        $this->assertStringNotContainsString('alt="QR Verifikasi TTE"', $draftHtml);
        $this->assertStringNotContainsString('Ditandatangani secara elektronik oleh:', $draftHtml);
        $this->assertStringNotContainsString(LetterDocumentService::FOOTER_TEXT, $draftHtml);

        $signedHtml = app(LetterDocumentService::class)->renderHtml($signedRequest, true);
        $this->assertStringContainsString('alt="QR Verifikasi TTE"', $signedHtml);
        $this->assertStringContainsString('data:image/png;base64,', $signedHtml);
        $this->assertStringContainsString('Ditandatangani secara elektronik oleh:', $signedHtml);
        $this->assertStringContainsString(LetterDocumentService::FOOTER_TEXT, $signedHtml);
        $expectedHost = preg_replace('/^www\./i', '', (string) parse_url(config('app.url'), PHP_URL_HOST));
        $this->assertStringContainsString('<p class="address-line">'.$expectedHost.'</p>', $signedHtml);
        $this->assertStringNotContainsString('<div>'.$expectedHost.'</div>', $signedHtml);
        $this->assertStringContainsString('<a href="'.route('letters.verify', $signedRequest->verification_token).'"', $signedHtml);

        Storage::disk('local')->assertMissing('service-requests/'.$signedRequest->id.'/to-sign.pdf');

        $this->assertSame(ServiceRequest::TTE_SIGNED, $signedRequest->tte_status);
        $this->assertSame('Selesai', $signedRequest->status);
        $this->assertSame(hash('sha256', $signedBinary), $signedRequest->signed_pdf_hash);
        $this->assertSame('730701******0001', $signedRequest->signer_nik_masked);
        Storage::disk('local')->assertExists($signedRequest->signed_pdf_path);

        // Pastikan passphrase tidak pernah tersimpan di tabel service_requests maupun audit_logs
        $auditContent = json_encode(AuditLog::all()->toArray());
        $this->assertStringNotContainsString($secretPassphrase, $auditContent);
        $this->assertStringNotContainsString('7307019999880001', $auditContent);
    }

    public function test_bsre_mock_mode_and_failure_handling(): void
    {
        config(['bsre.mock' => true]);

        $official = Official::create([
            'name' => 'Kepala Desa',
            'position' => 'Kepala Desa',
            'level' => 1,
            'order' => 1,
        ]);

        $preset = LetterTemplatePresets::get('domisili');
        $service = Service::create([
            'title' => 'Surat Domisili',
            'description' => 'Domisili',
            'letter_code' => $preset['letter_code'],
            'letter_title' => $preset['letter_title'],
            'letter_template' => $preset['letter_template'],
            'form_fields' => $preset['form_fields'],
            'is_tte_enabled' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'nik' => '7307011234560004',
            'name' => 'Warga Uji',
            'phone' => '081234567893',
            'service_id' => $service->id,
            'form_data' => ['keperluan' => 'Uji coba'],
        ]);

        $letterService = app(LetterDocumentService::class);

        $this->expectException(BsreException::class);
        $letterService->signWithBsre($serviceRequest, $official, '7307019999880002', 'invalid');
    }

    public function test_citizen_download_verification_page_revocation_and_cleanup(): void
    {
        config(['bsre.mock' => true]);

        $official = Official::create([
            'name' => 'H. Andi Muh. Rizal',
            'position' => 'Kepala Desa',
            'level' => 1,
            'order' => 1,
        ]);

        $preset = LetterTemplatePresets::get('tidak_mampu');
        $service = Service::create([
            'title' => 'Surat Keterangan Tidak Mampu',
            'description' => 'SKTM',
            'letter_code' => $preset['letter_code'],
            'letter_title' => $preset['letter_title'],
            'letter_template' => $preset['letter_template'],
            'form_fields' => $preset['form_fields'],
            'is_tte_enabled' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'nik' => '7307011234560005',
            'name' => 'Nurhaliza',
            'phone' => '081234567894',
            'service_id' => $service->id,
            'form_data' => ['keperluan' => 'Beasiswa kuliah'],
        ]);

        $letterService = app(LetterDocumentService::class);
        $signed = $letterService->signWithBsre($serviceRequest, $official, '7307019999880003', 'pass123');

        // 1. Endpoint lacak JSON mengembalikan status TTE & izin unduh
        $trackJson = $this->getJson(route('service-requests.track', ['ticket_number' => $signed->ticket_number]));
        $trackJson->assertOk()
            ->assertJson([
                'found' => true,
                'tte_status' => ServiceRequest::TTE_SIGNED,
                'can_download_letter' => true,
                'letter_number' => $signed->letter_number,
            ]);

        // 2. Unduh ditolak jika NIK tidak cocok
        $wrongNikResponse = $this->post(route('service-requests.download-letter'), [
            'ticket_number' => $signed->ticket_number,
            'nik' => '7307010000000000',
        ]);
        $wrongNikResponse->assertSessionHasErrors('download_nik');

        // 3. Unduh berhasil jika NIK sesuai
        $okDownload = $this->post(route('service-requests.download-letter'), [
            'ticket_number' => $signed->ticket_number,
            'nik' => '7307011234560005',
        ]);
        $okDownload->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $okDownload->headers->get('content-type'));
        $this->assertStringContainsString(
            'surat-keterangan-tidak-mampu-nurhaliza-',
            (string) $okDownload->headers->get('content-disposition')
        );
        $this->assertStringContainsString(
            '-tte.pdf',
            (string) $okDownload->headers->get('content-disposition')
        );

        // 4. Halaman verifikasi publik tampil sah tanpa tautan unduh PDF
        $verifyOk = $this->get(route('letters.verify', $signed->verification_token));
        $verifyOk->assertOk()
            ->assertSee('Dokumen Terverifikasi')
            ->assertSee($signed->letter_number)
            ->assertSee($signed->signed_pdf_hash)
            ->assertDontSee($signed->maskedNik())
            ->assertDontSee($signed->nik)
            ->assertDontSee(route('service-requests.download-letter'));

        // 5. Cabut surat TTE: unduhan warga ditolak dan halaman verifikasi tampil dicabut
        $letterService->revoke($signed, 'Kesalahan penulisan nama pemohon');

        $revokedDownload = $this->post(route('service-requests.download-letter'), [
            'ticket_number' => $signed->ticket_number,
            'nik' => '7307011234560005',
        ]);
        $revokedDownload->assertSessionHasErrors('download_nik');

        $verifyRevoked = $this->get(route('letters.verify', $signed->verification_token));
        $verifyRevoked->assertOk()
            ->assertSee('Dokumen Dicabut')
            ->assertSee('Kesalahan penulisan nama pemohon');

        // 6. Hapus permohonan membersihkan berkas fisik draft.pdf dan signed.pdf
        $draftPath = $signed->draft_pdf_path;
        $signedPath = $signed->signed_pdf_path;
        $signed->delete();

        Storage::disk('local')->assertMissing($draftPath);
        Storage::disk('local')->assertMissing($signedPath);
    }

    public function test_bsre_service_check_user_status_in_mock_mode(): void
    {
        config(['bsre.mock' => true]);

        $status = app(BsreService::class)->checkUserStatus('7307019999880003');
        $this->assertTrue($status['registered']);
        $this->assertSame('ACTIVE', $status['status']);
    }

    public function test_admin_letter_routes_enforce_authorization_and_serve_pdf(): void
    {
        config(['bsre.mock' => true]);

        $official = Official::create([
            'name' => 'H. Andi Muh. Rizal',
            'position' => 'Kepala Desa',
            'level' => 1,
            'order' => 1,
        ]);

        $preset = LetterTemplatePresets::get('domisili');
        $service = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Domisili',
            'letter_code' => $preset['letter_code'],
            'letter_title' => $preset['letter_title'],
            'letter_template' => $preset['letter_template'],
            'form_fields' => $preset['form_fields'],
            'is_tte_enabled' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'nik' => '7307011234560006',
            'name' => 'Rahman',
            'phone' => '081234567895',
            'service_id' => $service->id,
            'form_data' => ['keperluan' => 'Administrasi'],
        ]);

        $signed = app(LetterDocumentService::class)->signWithBsre(
            $serviceRequest,
            $official,
            '7307019999880004',
            'pass123'
        );

        $previewUrl = route('admin.service-request-letters.preview', [
            'serviceRequest' => $signed->id,
            'type' => 'signed',
        ]);
        $downloadUrl = route('admin.service-request-letters.download', [
            'serviceRequest' => $signed->id,
            'type' => 'signed',
        ]);

        // Guest diarahkan ke login
        $this->get($previewUrl)->assertRedirect();

        // User biasa tanpa izin ditolak 403
        $regularUser = User::factory()->create();
        $this->actingAs($regularUser)->get($previewUrl)->assertForbidden();
        $this->actingAs($regularUser)->get($downloadUrl)->assertForbidden();

        // Super Admin diizinkan melihat dan mengunduh PDF
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin)
            ->get($previewUrl)
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->actingAs($superAdmin)
            ->get($downloadUrl)
            ->assertOk();
    }

    public function test_download_nik_validation_and_sensitive_setting_exclusion(): void
    {
        Setting::updateOrCreate(['key' => 'tte_default_signer_nik'], ['value' => '7307019999887777']);

        $response = $this->post(route('service-requests.download-letter'), [
            'ticket_number' => 'SRV-20261125-ABC123',
            'download_nik' => '12345',
        ]);

        $response->assertRedirect(route('service-requests.track', ['ticket_number' => 'SRV-20261125-ABC123']));
        $response->assertSessionHasErrors('download_nik');

        $this->assertFalse(app(LetterDocumentService::class)->isGlobalTteEnabled());

        Setting::updateOrCreate(['key' => 'tte_enabled'], ['value' => '1']);
        $this->assertTrue(app(LetterDocumentService::class)->isGlobalTteEnabled());

        // Pastikan view publik layanan tidak membocorkan NIK penanda tangan ke $site_settings
        Cache::forget('site_settings_all');
        (new AppServiceProvider(app()))->boot();
        $sharedSettings = View::getShared()['site_settings'] ?? [];
        $this->assertArrayNotHasKey('tte_default_signer_nik', $sharedSettings);

        $layananResponse = $this->get(route('layanan'));
        $layananResponse->assertOk();
        $layananResponse->assertDontSee('7307019999887777');
    }

    public function test_revoked_letter_preserves_revoked_status_on_failed_resign_and_rotates_token_on_success(): void
    {
        config(['bsre.mock' => true]);

        $official = Official::create([
            'name' => 'H. Andi Muh. Rizal',
            'position' => 'Kepala Desa',
            'level' => 1,
            'order' => 1,
        ]);

        $preset = LetterTemplatePresets::get('domisili');
        $service = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Domisili',
            'letter_code' => $preset['letter_code'],
            'letter_title' => $preset['letter_title'],
            'letter_template' => $preset['letter_template'],
            'form_fields' => $preset['form_fields'],
            'is_tte_enabled' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'nik' => '7307011234560007',
            'name' => 'Samsul',
            'phone' => '081234567896',
            'service_id' => $service->id,
            'form_data' => ['keperluan' => 'Pendaftaran sekolah'],
        ]);

        $letterService = app(LetterDocumentService::class);
        $signed = $letterService->signWithBsre($serviceRequest, $official, '7307019999880005', 'pass123');
        $oldToken = $signed->verification_token;

        $revoked = $letterService->revoke($signed, 'Kesalahan data alamat');

        // Membuat draf ulang pada surat dicabut tidak boleh menimpa status revoked
        $letterService->generateDraftPdf($revoked);
        $revoked->refresh();
        $this->assertSame(ServiceRequest::TTE_REVOKED, $revoked->tte_status);
        $this->assertSame($oldToken, $revoked->verification_token);
        $this->get(route('letters.verify', $oldToken))
            ->assertOk()
            ->assertSee('Dokumen Dicabut');

        // Gagal TTE ulang harus mempertahankan status revoked dan token lama
        try {
            $letterService->signWithBsre($revoked, $official, '7307019999880005', 'invalid');
            $this->fail('BsreException seharusnya dilempar saat passphrase tidak valid.');
        } catch (BsreException) {
            // Diharapkan gagal
        }

        $revoked->refresh();
        $this->assertSame(ServiceRequest::TTE_REVOKED, $revoked->tte_status);
        $this->assertSame($oldToken, $revoked->verification_token);
        $this->assertSame('Kesalahan data alamat', $revoked->revocation_reason);
        $this->get(route('letters.verify', $oldToken))
            ->assertOk()
            ->assertSee('Dokumen Dicabut');

        // Berhasil TTE ulang merotasi token verifikasi sehingga QR lama tidak lagi sah
        $resigned = $letterService->signWithBsre($revoked, $official, '7307019999880005', 'pass123');
        $this->assertSame(ServiceRequest::TTE_SIGNED, $resigned->tte_status);
        $this->assertNotSame($oldToken, $resigned->verification_token);
        $this->get(route('letters.verify', $oldToken))->assertNotFound();
        $this->get(route('letters.verify', $resigned->verification_token))
            ->assertOk()
            ->assertSee('Dokumen Terverifikasi');
    }

    public function test_render_html_syncs_updated_applicant_identity_and_formats_date_form_fields(): void
    {
        $service = Service::create([
            'title' => 'Surat Izin Keramaian',
            'description' => 'Izin Keramaian',
            'letter_code' => '300',
            'letter_title' => 'SURAT IZIN KERAMAIAN',
            'letter_template' => '<p>Nama: {{ applicant_name }} | NIK: {{ applicant_nik }} | Tanggal: {{ tanggal_acara }}</p>',
            'form_fields' => [
                [
                    'key' => 'tanggal_acara',
                    'label' => 'Tanggal Acara',
                    'type' => 'date',
                    'is_required' => true,
                ],
            ],
            'is_tte_enabled' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'nik' => '7307011234560008',
            'name' => 'Budi Lama',
            'phone' => '081234567897',
            'service_id' => $service->id,
            'form_data' => ['tanggal_acara' => '2026-11-20'],
        ]);

        $letterService = app(LetterDocumentService::class);
        $letterService->generateDraftPdf($serviceRequest);

        $firstHtml = $letterService->renderHtml($serviceRequest->fresh());
        $this->assertStringContainsString('Budi Lama', $firstHtml);
        $this->assertStringContainsString('7307011234560008', $firstHtml);
        $this->assertStringContainsString('20 November 2026', $firstHtml);
        $this->assertStringNotContainsString('2026-11-20', $firstHtml);

        $serviceRequest->update([
            'name' => 'Budi Baru',
            'nik' => '7307011234560009',
        ]);

        $letterService->generateDraftPdf($serviceRequest->fresh());
        $updated = $serviceRequest->fresh();

        $this->assertSame('Budi Baru', $updated->letter_payload['applicant_name']);
        $this->assertSame('7307011234560009', $updated->letter_payload['applicant_nik']);

        $secondHtml = $letterService->renderHtml($updated);
        $this->assertStringContainsString('Budi Baru', $secondHtml);
        $this->assertStringContainsString('7307011234560009', $secondHtml);
    }

    public function test_render_html_normalizes_region_prefixes_handles_missing_citizen_and_preserves_payload_overrides(): void
    {
        Setting::updateOrCreate(['key' => 'village_name'], ['value' => 'Desa Tompobulu']);
        Setting::updateOrCreate(['key' => 'district_name'], ['value' => 'Kecamatan Bulupoddo']);
        Setting::updateOrCreate(['key' => 'regency_name'], ['value' => 'Kabupaten Sinjai']);

        $preset = LetterTemplatePresets::get('domisili');
        $service = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Domisili',
            'letter_code' => $preset['letter_code'],
            'letter_title' => $preset['letter_title'],
            'letter_template' => $preset['letter_template'],
            'form_fields' => $preset['form_fields'],
            'is_tte_enabled' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'nik' => '7307011234560010',
            'name' => 'Warga Tanpa Data Kependudukan',
            'phone' => '081234567898',
            'service_id' => $service->id,
            'form_data' => [
                'keperluan' => 'Keperluan awal warga',
            ],
        ]);

        $letterService = app(LetterDocumentService::class);
        $defaultPayload = $letterService->buildDefaultPayload($serviceRequest);
        $this->assertArrayNotHasKey('keperluan', $defaultPayload);

        $htmlWithoutCitizen = $letterService->renderHtml($serviceRequest);
        $this->assertStringContainsString('alt="Logo Kabupaten Sinjai"', $htmlWithoutCitizen);
        $this->assertStringContainsString('data:image/png;base64,', $htmlWithoutCitizen);
        $this->assertStringContainsString('PEMERINTAH KABUPATEN SINJAI', $htmlWithoutCitizen);
        $this->assertStringContainsString('KECAMATAN BULUPODDO', $htmlWithoutCitizen);
        $this->assertStringContainsString('PEMERINTAH DESA TOMPOBULU', $htmlWithoutCitizen);
        $this->assertStringNotContainsString('Kabupaten Kabupaten', $htmlWithoutCitizen);
        $this->assertStringNotContainsString('Kecamatan Kecamatan', $htmlWithoutCitizen);
        $this->assertStringNotContainsString('Desa Desa', $htmlWithoutCitizen);
        $this->assertStringNotContainsString('-, -', $htmlWithoutCitizen);
        $this->assertStringNotContainsString('- RT - RW -', $htmlWithoutCitizen);
        $this->assertStringContainsString('Keperluan awal warga', $htmlWithoutCitizen);

        // Override petugas pada letter_payload tidak boleh tertimpa oleh form_data asli
        $serviceRequest->update([
            'letter_payload' => [
                'keperluan' => 'Keperluan dikoreksi petugas',
            ],
        ]);

        $htmlWithOverride = $letterService->renderHtml($serviceRequest->fresh());
        $this->assertStringContainsString('Keperluan dikoreksi petugas', $htmlWithOverride);
        $this->assertStringNotContainsString('Keperluan awal warga', $htmlWithOverride);
    }

    public function test_service_request_cannot_be_created_from_admin_panel(): void
    {
        $this->assertFalse(ServiceRequestResource::canCreate());

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin)
            ->get('/admin/service-requests/create')
            ->assertNotFound();

        $this->actingAs($superAdmin)
            ->get('/admin/service-requests')
            ->assertOk()
            ->assertDontSee('Buat Permohonan Layanan');
    }

    public function test_sync_citizen_payload_returns_false_without_overwriting_manual_data_when_nik_missing(): void
    {
        $service = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Domisili',
            'is_tte_enabled' => true,
        ]);

        $missingRequest = ServiceRequest::create([
            'nik' => '7307011234560098',
            'name' => 'Warga Belum Terdaftar',
            'phone' => '081234567899',
            'service_id' => $service->id,
        ]);

        $letterService = app(LetterDocumentService::class);

        // NIK tidak terdaftar dan payload masih kosong -> mengembalikan false dan inisialisasi kerangka standar
        $this->assertFalse($letterService->syncCitizenPayload($missingRequest));
        $missingRequest->refresh();
        $this->assertSame('Warga Belum Terdaftar', $missingRequest->letter_payload['applicant_name']);
        $this->assertSame('', $missingRequest->letter_payload['place_of_birth']);

        // Petugas mengisi manual, lalu klik Isi Data Warga lagi -> data manual tidak tertimpa
        $missingRequest->update([
            'letter_payload' => array_merge($missingRequest->letter_payload, [
                'place_of_birth' => 'Sinjai',
                'custom_note' => 'Catatan petugas',
            ]),
        ]);

        $this->assertFalse($letterService->syncCitizenPayload($missingRequest->fresh()));
        $missingRequest->refresh();
        $this->assertSame('Sinjai', $missingRequest->letter_payload['place_of_birth']);
        $this->assertSame('Catatan petugas', $missingRequest->letter_payload['custom_note']);

        // Jika warga terdaftar di tabel citizens -> mengembalikan true, mengisi data kependudukan, dan mempertahankan key kustom
        $dusun = Dusun::create(['name' => 'Bulu', 'total_rt' => 2, 'total_rw' => 1]);
        Citizen::create([
            'nik' => '7307011234560098',
            'dusun_id' => $dusun->id,
            'name' => 'Warga Terdaftar',
            'place_of_birth' => 'Makassar',
            'date_of_birth' => '1992-03-15',
            'gender' => 'Laki-laki',
            'religion' => 'Islam',
            'job' => 'Petani',
            'address' => 'Jalan Desa',
            'rt' => '001',
            'rw' => '001',
            'status' => 'Aktif',
        ]);

        $this->assertTrue($letterService->syncCitizenPayload($missingRequest->fresh()));
        $missingRequest->refresh();
        $this->assertSame('Makassar', $missingRequest->letter_payload['place_of_birth']);
        $this->assertSame('Bulu', $missingRequest->letter_payload['dusun']);
        $this->assertSame('Catatan petugas', $missingRequest->letter_payload['custom_note']);
    }

    public function test_generate_draft_pdf_action_updates_edit_page_live_without_reload(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $preset = LetterTemplatePresets::get('domisili');
        $service = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Domisili',
            'letter_code' => $preset['letter_code'],
            'letter_title' => $preset['letter_title'],
            'letter_template' => $preset['letter_template'],
            'form_fields' => $preset['form_fields'],
            'is_tte_enabled' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'nik' => '7307011234560011',
            'name' => 'Hasanuddin',
            'phone' => '081234567891',
            'service_id' => $service->id,
            'form_data' => ['keperluan' => 'Administrasi bank'],
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(EditServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSee('Belum Dibuat')
            ->assertDontSee('Lihat Draf')
            ->callAction('generateDraftPdf')
            ->assertHasNoActionErrors()
            ->assertSee('Draf')
            ->assertSee('Lihat Draf')
            ->assertSet('data.tte_status', ServiceRequest::TTE_DRAFT);

        $fresh = $serviceRequest->fresh();
        $this->assertSame(ServiceRequest::TTE_DRAFT, $fresh->tte_status);
        $this->assertNotNull($fresh->draft_pdf_path);
    }

    public function test_whatsapp_notification_handles_signed_revoked_and_rejected_statuses(): void
    {
        $service = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Domisili',
            'is_tte_enabled' => true,
        ]);

        $signedRequest = ServiceRequest::create([
            'nik' => '7307011234560012',
            'name' => 'Ani',
            'phone' => '081234567801',
            'service_id' => $service->id,
            'status' => 'Selesai',
            'tte_status' => ServiceRequest::TTE_SIGNED,
        ]);
        $signedWa = $signedRequest->buildWhatsAppNotification();
        $this->assertNotNull($signedWa);
        $this->assertSame('#25d366', $signedWa['color']);
        $this->assertStringContainsString('telah+selesai+ditandatangani+secara+elektronik', $signedWa['url']);

        $revokedRequest = ServiceRequest::create([
            'nik' => '7307011234560013',
            'name' => 'Budi',
            'phone' => '081234567802',
            'service_id' => $service->id,
            'status' => 'Selesai',
            'tte_status' => ServiceRequest::TTE_REVOKED,
            'revocation_reason' => 'Salah ketik nama',
        ]);
        $revokedWa = $revokedRequest->buildWhatsAppNotification();
        $this->assertNotNull($revokedWa);
        $this->assertSame('#e11d48', $revokedWa['color']);
        $this->assertStringContainsString('DICABUT', $revokedWa['url']);
        $this->assertStringContainsString('Salah+ketik+nama', $revokedWa['url']);
        $this->assertStringNotContainsString('SELESAI', $revokedWa['url']);

        $rejectedRequest = ServiceRequest::create([
            'nik' => '7307011234560014',
            'name' => 'Cici',
            'phone' => '081234567803',
            'service_id' => $service->id,
            'status' => 'Ditolak',
            'admin_response' => 'Foto KTP buram',
        ]);
        $rejectedWa = $rejectedRequest->buildWhatsAppNotification();
        $this->assertNotNull($rejectedWa);
        $this->assertSame('#e11d48', $rejectedWa['color']);
        $this->assertStringContainsString('ditolak', $rejectedWa['url']);
        $this->assertStringContainsString('Foto+KTP+buram', $rejectedWa['url']);
    }

    public function test_draft_pdf_hides_tte_draft_watermark_when_global_tte_disabled(): void
    {
        $service = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Domisili',
            'letter_code' => '470',
            'letter_title' => 'SURAT KETERANGAN DOMISILI',
            'letter_template' => '<p>Nama: {{ applicant_name }}</p>',
            'is_tte_enabled' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'nik' => '7307011234560015',
            'name' => 'Dedi',
            'phone' => '081234567804',
            'service_id' => $service->id,
        ]);

        $letterService = app(LetterDocumentService::class);

        Setting::updateOrCreate(['key' => 'tte_enabled'], ['value' => '0']);
        $wetSignHtml = $letterService->renderHtml($serviceRequest, false);
        $this->assertStringNotContainsString('Draf belum ditandatangani secara elektronik', $wetSignHtml);

        Setting::updateOrCreate(['key' => 'tte_enabled'], ['value' => '1']);
        $tteDraftHtml = $letterService->renderHtml($serviceRequest, false);
        $this->assertStringContainsString('Draf belum ditandatangani secara elektronik', $tteDraftHtml);
    }

    public function test_track_endpoint_returns_rejected_status_and_admin_response(): void
    {
        $service = Service::create([
            'title' => 'Surat Keterangan Usaha',
            'description' => 'SKU',
            'is_tte_enabled' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'nik' => '7307011234560016',
            'name' => 'Eka',
            'phone' => '081234567805',
            'service_id' => $service->id,
            'status' => 'Ditolak',
            'admin_response' => 'Lampiran foto usaha belum diunggah',
        ]);

        $this->getJson(route('service-requests.track', ['ticket_number' => $serviceRequest->ticket_number]))
            ->assertOk()
            ->assertJson([
                'found' => true,
                'status' => 'Ditolak',
                'admin_response' => 'Lampiran foto usaha belum diunggah',
            ]);
    }

    public function test_tte_guards_prevent_double_sign_rejected_sign_and_invalid_revoke(): void
    {
        config(['bsre.mock' => true]);

        $official = Official::create([
            'name' => 'Kepala Desa',
            'position' => 'Kepala Desa',
            'level' => 1,
            'order' => 1,
        ]);

        $service = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Domisili',
            'is_tte_enabled' => true,
        ]);

        $letterService = app(LetterDocumentService::class);

        $signedRequest = ServiceRequest::create([
            'nik' => '7307011234560017',
            'name' => 'Fajar',
            'phone' => '081234567806',
            'service_id' => $service->id,
        ]);
        $signed = $letterService->signWithBsre($signedRequest, $official, '7307019999880006', 'pass123');

        // 1. Tolak double-sign tanpa pencabutan
        try {
            $letterService->signWithBsre($signed, $official, '7307019999880006', 'pass123');
            $this->fail('BsreException seharusnya dilempar saat menandatangani ulang surat yang masih aktif.');
        } catch (BsreException $e) {
            $this->assertStringContainsString('sudah ditandatangani', $e->getMessage());
        }

        // 2. Tolak penandatanganan pada permohonan berstatus Ditolak
        $rejectedRequest = ServiceRequest::create([
            'nik' => '7307011234560018',
            'name' => 'Gita',
            'phone' => '081234567807',
            'service_id' => $service->id,
            'status' => 'Ditolak',
        ]);

        try {
            $letterService->signWithBsre($rejectedRequest, $official, '7307019999880006', 'pass123');
            $this->fail('BsreException seharusnya dilempar untuk permohonan berstatus Ditolak.');
        } catch (BsreException $e) {
            $this->assertStringContainsString('ditolak', $e->getMessage());
        }

        // 3. Tolak pencabutan pada surat yang belum TTE_SIGNED
        $this->expectException(\RuntimeException::class);
        $letterService->revoke($rejectedRequest, 'Tidak valid');
    }

    public function test_failed_bsre_signing_records_audit_log_without_leaking_credentials(): void
    {
        config(['bsre.mock' => true]);

        $official = Official::create([
            'name' => 'Kepala Desa',
            'position' => 'Kepala Desa',
            'level' => 1,
            'order' => 1,
        ]);

        $service = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Domisili',
            'is_tte_enabled' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'nik' => '7307011234560019',
            'name' => 'Hendra',
            'phone' => '081234567808',
            'service_id' => $service->id,
        ]);

        try {
            app(LetterDocumentService::class)->signWithBsre($serviceRequest, $official, '7307019999880007', 'invalid');
            $this->fail('BsreException seharusnya dilempar.');
        } catch (BsreException) {
            // Diharapkan gagal
        }

        $failedAudit = AuditLog::where('event', 'tte_sign_failed')
            ->where('auditable_id', $serviceRequest->id)
            ->first();

        $this->assertNotNull($failedAudit);
        $serialized = json_encode($failedAudit->toArray());
        $this->assertStringNotContainsString('7307019999880007', $serialized);
        $this->assertStringNotContainsString('invalid', strtolower((string) ($failedAudit->new_values['passphrase'] ?? '')));
    }

    public function test_edit_page_hides_tte_actions_when_rejected_and_enforces_unique_letter_number(): void
    {
        Setting::updateOrCreate(['key' => 'tte_enabled'], ['value' => '1']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $service = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Domisili',
            'is_tte_enabled' => true,
        ]);

        ServiceRequest::create([
            'nik' => '7307011234560020',
            'name' => 'Indra',
            'phone' => '081234567809',
            'service_id' => $service->id,
            'letter_number' => '470/001/TPB/XI/2026',
        ]);

        $targetRequest = ServiceRequest::create([
            'nik' => '7307011234560021',
            'name' => 'Joko',
            'phone' => '081234567810',
            'service_id' => $service->id,
            'status' => 'Ditolak',
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(EditServiceRequest::class, ['record' => $targetRequest->getRouteKey()])
            ->assertActionHidden('generateDraftPdf')
            ->assertActionHidden('signWithBsre')
            ->fillForm([
                'letter_number' => '470/001/TPB/XI/2026',
            ])
            ->call('save')
            ->assertHasFormErrors(['letter_number' => 'unique']);
    }

    public function test_edit_page_renders_dynamic_form_data_fields_and_formats_iso_date_of_birth(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $preset = LetterTemplatePresets::get('domisili');
        $service = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Domisili',
            'letter_code' => $preset['letter_code'],
            'letter_title' => $preset['letter_title'],
            'letter_template' => $preset['letter_template'],
            'form_fields' => $preset['form_fields'],
            'is_tte_enabled' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'nik' => '7307011234560022',
            'name' => 'Kartini',
            'phone' => '081234567811',
            'service_id' => $service->id,
            'form_data' => [
                'keperluan' => 'Administrasi bank',
            ],
            'letter_payload' => [
                'place_of_birth' => 'Sinjai',
                'date_of_birth' => '1995-05-12',
            ],
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(EditServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSee('Keperluan Surat')
            ->fillForm([
                'form_data' => [
                    'keperluan' => 'Pengurusan beasiswa',
                ],
                'letter_payload' => [
                    'place_of_birth' => 'Sinjai',
                    'date_of_birth' => '1995-05-12',
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $updated = $serviceRequest->fresh();
        $this->assertSame('Pengurusan beasiswa', $updated->form_data['keperluan']);
        $this->assertSame('1995-05-12', $updated->letter_payload['date_of_birth']);

        $htmlIso = app(LetterDocumentService::class)->renderHtml($updated);
        $this->assertStringContainsString('12 Mei 1995', $htmlIso);

        // Kompatibilitas mundur untuk data lama yang sudah berformat teks Indonesia
        $updated->update([
            'letter_payload' => [
                'place_of_birth' => 'Sinjai',
                'date_of_birth' => '12 Mei 1995',
            ],
        ]);
        $htmlLegacy = app(LetterDocumentService::class)->renderHtml($updated->fresh());
        $this->assertStringContainsString('12 Mei 1995', $htmlLegacy);
    }

    public function test_draft_pdf_previews_letter_number_without_incrementing_sequence_when_global_tte_enabled(): void
    {
        config(['bsre.mock' => true]);
        Setting::updateOrCreate(['key' => 'tte_enabled'], ['value' => '1']);

        $official = Official::create([
            'name' => 'Kepala Desa',
            'position' => 'Kepala Desa',
            'level' => 1,
            'order' => 1,
        ]);

        $service = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Domisili',
            'letter_code' => '400.12.2',
            'letter_title' => 'SURAT KETERANGAN DOMISILI',
            'letter_template' => '<p>Nama: {{ applicant_name }}</p>',
            'is_tte_enabled' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'nik' => '7307011234560023',
            'name' => 'Laila',
            'phone' => '081234567812',
            'service_id' => $service->id,
        ]);

        $letterService = app(LetterDocumentService::class);
        $letterService->generateDraftPdf($serviceRequest);
        $letterService->generateDraftPdf($serviceRequest->fresh());

        $afterDraft = $serviceRequest->fresh();
        $this->assertSame(ServiceRequest::TTE_DRAFT, $afterDraft->tte_status);
        $this->assertNull($afterDraft->letter_number);
        $this->assertSame(0, (int) (LetterSequence::where('year', now()->year)->value('last_number') ?? 0));

        $signed = $letterService->signWithBsre($afterDraft, $official, '7307019999880008', 'pass123');
        $this->assertSame(1, (int) LetterSequence::where('year', now()->year)->value('last_number'));
        $this->assertStringContainsString('/001/TPB/', (string) $signed->letter_number);
    }
}
