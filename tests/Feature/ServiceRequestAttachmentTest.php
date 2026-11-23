<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServiceRequestAttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');
    }

    public function test_service_without_requirement_items_can_be_submitted_without_files(): void
    {
        $service = Service::create([
            'title' => 'Surat Keterangan Umum',
            'description' => 'Layanan umum tanpa unggahan berkas.',
        ]);

        $response = $this->post(route('service-requests.store'), [
            'nik' => '7307011234560001',
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'service_id' => $service->id,
            '_hp_website' => '',
        ]);

        $response->assertRedirect(route('layanan'));
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('service_requests', 1);
        $this->assertDatabaseCount('service_request_attachments', 0);
    }

    public function test_required_attachment_is_validated_strictly(): void
    {
        $service = Service::create([
            'title' => 'Surat Keterangan Usaha',
            'description' => 'Layanan SKU',
        ]);

        $reqKtp = $service->requirementItems()->create([
            'name' => 'Foto KTP Pemohon',
            'is_required' => true,
            'sort_order' => 1,
        ]);

        // Gagal jika berkas wajib tidak diunggah
        $response = $this->post(route('service-requests.store'), [
            'nik' => '7307011234560001',
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'service_id' => $service->id,
            '_hp_website' => '',
        ]);

        $response->assertSessionHasErrors('attachments.'.$reqKtp->id);
        $this->assertDatabaseCount('service_requests', 0);

        // Gagal jika ekstensi terlarang (misal PHP / teks)
        $badFile = UploadedFile::fake()->create('shell.php', 10, 'application/x-php');
        $response = $this->post(route('service-requests.store'), [
            'nik' => '7307011234560001',
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'service_id' => $service->id,
            'attachments' => [
                $reqKtp->id => $badFile,
            ],
            '_hp_website' => '',
        ]);

        $response->assertSessionHasErrors('attachments.'.$reqKtp->id);
        $this->assertDatabaseCount('service_requests', 0);

        // Gagal jika ukuran melebihi 2 MB (2048 KB)
        $oversizedPdf = UploadedFile::fake()->create('ktp.pdf', 2100, 'application/pdf');
        $response = $this->post(route('service-requests.store'), [
            'nik' => '7307011234560001',
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'service_id' => $service->id,
            'attachments' => [
                $reqKtp->id => $oversizedPdf,
            ],
            '_hp_website' => '',
        ]);

        $response->assertSessionHasErrors('attachments.'.$reqKtp->id);
        $this->assertDatabaseCount('service_requests', 0);
    }

    public function test_valid_image_and_pdf_attachments_are_stored_privately_and_tracked_safely(): void
    {
        $service = Service::create([
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Layanan domisili warga.',
        ]);

        $reqKtp = $service->requirementItems()->create([
            'name' => 'Foto KTP Pemohon',
            'description' => 'Foto asli terbaca jelas',
            'is_required' => true,
            'sort_order' => 1,
        ]);

        $reqPengantar = $service->requirementItems()->create([
            'name' => 'Surat Pengantar RT',
            'is_required' => true,
            'sort_order' => 2,
        ]);

        $reqOpsional = $service->requirementItems()->create([
            'name' => 'Foto Rumah',
            'is_required' => false,
            'sort_order' => 3,
        ]);

        $ktpImage = UploadedFile::fake()->image('ktp-asli.jpg', 1200, 800);
        $pengantarPdf = UploadedFile::fake()->create('pengantar-rt.pdf', 250, 'application/pdf');

        $response = $this->post(route('service-requests.store'), [
            'nik' => '7307011234560001',
            'name' => 'Siti Aminah',
            'phone' => '081234567890',
            'service_id' => $service->id,
            'attachments' => [
                $reqKtp->id => $ktpImage,
                $reqPengantar->id => $pengantarPdf,
            ],
            '_hp_website' => '',
        ]);

        $response->assertRedirect(route('layanan'));
        $response->assertSessionHas('success');

        $serviceRequest = ServiceRequest::with('attachments')->first();
        $this->assertNotNull($serviceRequest);
        $this->assertCount(2, $serviceRequest->attachments);

        $ktpAttachment = $serviceRequest->attachments->firstWhere('service_requirement_id', $reqKtp->id);
        $pdfAttachment = $serviceRequest->attachments->firstWhere('service_requirement_id', $reqPengantar->id);

        $this->assertNotNull($ktpAttachment);
        $this->assertNotNull($pdfAttachment);
        $this->assertSame('Foto KTP Pemohon', $ktpAttachment->requirement_name);
        $this->assertSame('Surat Pengantar RT', $pdfAttachment->requirement_name);

        // Pastikan disimpan pada disk privat, bukan publik
        Storage::disk('local')->assertExists($ktpAttachment->file_path);
        Storage::disk('local')->assertExists($pdfAttachment->file_path);
        Storage::disk('public')->assertMissing($ktpAttachment->file_path);
        Storage::disk('public')->assertMissing($pdfAttachment->file_path);

        if (function_exists('imagewebp')) {
            $this->assertStringEndsWith('.webp', $ktpAttachment->file_path);
            $this->assertSame('image/webp', $ktpAttachment->mime_type);
        }

        $this->assertStringEndsWith('.pdf', $pdfAttachment->file_path);

        // Pastikan endpoint lacak hanya mengembalikan nama persyaratan tanpa path/URL
        $trackResponse = $this->getJson(route('service-requests.track', [
            'ticket_number' => $serviceRequest->ticket_number,
        ]));

        $trackResponse->assertOk()
            ->assertJson([
                'found' => true,
                'ticket_number' => $serviceRequest->ticket_number,
                'attachments' => ['Foto KTP Pemohon', 'Surat Pengantar RT'],
            ]);

        $this->assertStringNotContainsString($ktpAttachment->file_path, $trackResponse->getContent());

        // Pastikan berkas fisik ikut terhapus ketika permohonan dihapus
        $ktpPath = $ktpAttachment->file_path;
        $pdfPath = $pdfAttachment->file_path;

        $serviceRequest->delete();

        $this->assertDatabaseCount('service_request_attachments', 0);
        Storage::disk('local')->assertMissing($ktpPath);
        Storage::disk('local')->assertMissing($pdfPath);
    }

    public function test_private_attachment_routes_enforce_authentication_and_authorization(): void
    {
        $service = Service::create([
            'title' => 'Surat Pengantar KTP',
            'description' => 'Layanan KTP',
        ]);

        $serviceRequest = ServiceRequest::create([
            'nik' => '7307011234560001',
            'name' => 'Ahmad',
            'phone' => '081234567890',
            'service_id' => $service->id,
        ]);

        $filePath = "service-requests/{$serviceRequest->id}/test-doc.pdf";
        Storage::disk('local')->put($filePath, '%PDF-1.4 sample content');

        $attachment = ServiceRequestAttachment::create([
            'service_request_id' => $serviceRequest->id,
            'requirement_name' => 'Foto KK',
            'file_path' => $filePath,
            'original_name' => 'kk.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 23,
        ]);

        // 1. Tamu tidak login ditolak
        $this->get(route('admin.service-request-attachments.preview', $attachment))
            ->assertRedirect();
        $this->get(route('admin.service-request-attachments.download', $attachment))
            ->assertRedirect();

        // 2. Pengguna tanpa izin ditolak (403)
        $regularUser = User::factory()->create();
        $this->actingAs($regularUser)
            ->get(route('admin.service-request-attachments.preview', $attachment))
            ->assertForbidden();

        // 3. Operator admin_desa dengan izin View:ServiceRequest diizinkan
        Role::firstOrCreate(['name' => 'admin_desa', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'View:ServiceRequest', 'guard_name' => 'web']);
        $operator = User::factory()->create();
        $operator->assignRole('admin_desa');
        $operator->givePermissionTo('View:ServiceRequest');

        $this->actingAs($operator)
            ->get(route('admin.service-request-attachments.preview', $attachment))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->actingAs($operator)
            ->get(route('admin.service-request-attachments.download', $attachment))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        // 4. Super admin diizinkan
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin)
            ->get(route('admin.service-request-attachments.preview', $attachment))
            ->assertOk();

        // 5. Path traversal pada DB ditolak (404)
        $attachment->update(['file_path' => '../.env']);
        $this->actingAs($superAdmin)
            ->get(route('admin.service-request-attachments.preview', $attachment))
            ->assertNotFound();
    }

    public function test_layanan_page_displays_structured_requirement_items(): void
    {
        $service = Service::create([
            'title' => 'Surat Keterangan Usaha',
            'description' => 'Layanan SKU warga.',
        ]);

        $service->requirementItems()->create([
            'name' => 'Foto KTP Pemohon',
            'is_required' => true,
            'sort_order' => 1,
        ]);

        $service->requirementItems()->create([
            'name' => 'Foto Tempat Usaha',
            'is_required' => false,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('layanan'));

        $response->assertOk()
            ->assertSee('Berkas Unggahan')
            ->assertSee('Foto KTP Pemohon')
            ->assertSee('Foto Tempat Usaha')
            ->assertSee('Wajib')
            ->assertSee('Opsional');
    }
}
