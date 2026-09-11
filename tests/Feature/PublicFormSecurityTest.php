<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\GuestBook;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicFormSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_honeypot_rejects_automated_submission(): void
    {
        $response = $this->post(route('complaints.store'), [
            'name' => 'Spam Bot',
            'phone' => '081234567890',
            'title' => 'Spam Title',
            'content' => 'Spam Content',
            '_hp_website' => 'http://spam-link.com', // Bot fills the honeypot
        ]);

        $response->assertSessionHasErrors('form_error');
        $this->assertDatabaseMissing('complaints', ['name' => 'Spam Bot']);
    }

    public function test_time_gate_rejects_when_too_fast_or_tampered(): void
    {
        // Encrypt a timestamp that is only 1 second ago (fast bot submission)
        $tamperedTime = encrypt(time());

        // We run a test request with custom form_time
        $response = $this->withSession([])->post(route('complaints.store'), [
            'name' => 'Fast Bot',
            'phone' => '081234567890',
            'title' => 'Fast Bot Title',
            'content' => 'Fast Bot Content',
            '_hp_website' => '',
            '_form_time' => 'invalid-token',
        ]);

        $response->assertSessionHasErrors('form_error');
        $this->assertDatabaseMissing('complaints', ['name' => 'Fast Bot']);
    }

    public function test_complaint_submission_sanitizes_html_tags(): void
    {
        $response = $this->post(route('complaints.store'), [
            'name' => '<script>alert("xss")</script>Budi',
            'phone' => '0812-3456-7890',
            'title' => '<b>Lampu Jalan</b> Rusak',
            'content' => '<p>Mohon diperbaiki di <script>malicious()</script>jalan utama.</p>',
            '_hp_website' => '',
        ]);

        $response->assertRedirect(route('complaints.index'));
        $response->assertSessionHas('success');

        $complaint = Complaint::first();
        $this->assertNotNull($complaint);
        $this->assertEquals('Budi', $complaint->name);
        $this->assertEquals('081234567890', $complaint->phone);
        $this->assertEquals('Lampu Jalan Rusak', $complaint->title);
        $this->assertEquals('Mohon diperbaiki di jalan utama.', $complaint->content);
        // Assert ticket number has 6 hex random characters (ADV-YYYYMMDD-XXXXXX -> length: 4 + 8 + 1 + 6 = 19)
        $this->assertMatchesRegularExpression('/^ADV-\d{8}-[A-F0-9]{6}$/', $complaint->ticket_number);
    }

    public function test_guest_book_submission_sanitizes_html_tags(): void
    {
        $response = $this->post(route('guest-book.store'), [
            'name' => '<b>Drs. Ahmad</b>',
            'institution_address' => '<script>alert(1)</script>Dinas Pertanian',
            'phone' => '+62 812-9988-7766',
            'purpose' => '<i>Kunjungan kerja dinas</i>',
            '_hp_website' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $guest = GuestBook::first();
        $this->assertNotNull($guest);
        $this->assertEquals('Drs. Ahmad', $guest->name);
        $this->assertEquals('Dinas Pertanian', $guest->institution_address);
        $this->assertEquals('+6281299887766', $guest->phone);
        $this->assertEquals('Kunjungan kerja dinas', $guest->purpose);
    }

    public function test_service_request_validates_nik_strictly(): void
    {
        $service = Service::create([
            'title' => 'Surat Keterangan Usaha',
            'description' => 'Layanan SKU',
            'icon' => 'fa-file',
        ]);

        // NIK less than 16 digits
        $response = $this->post(route('service-requests.store'), [
            'nik' => '12345',
            'name' => 'Ahmad',
            'phone' => '081234567890',
            'service_id' => $service->id,
            '_hp_website' => '',
        ]);

        $response->assertSessionHasErrors('nik');
        $this->assertDatabaseCount('service_requests', 0);

        // NIK with letters
        $response = $this->post(route('service-requests.store'), [
            'nik' => '123456789012345A',
            'name' => 'Ahmad',
            'phone' => '081234567890',
            'service_id' => $service->id,
            '_hp_website' => '',
        ]);

        $response->assertSessionHasErrors('nik');
        $this->assertDatabaseCount('service_requests', 0);

        // Valid 16-digit NIK
        $response = $this->post(route('service-requests.store'), [
            'nik' => '7307011234560001',
            'name' => '<b>Ahmad Dahlan</b>',
            'phone' => '0812-3456-7890',
            'service_id' => $service->id,
            '_hp_website' => '',
        ]);

        $response->assertRedirect(route('layanan'));
        $response->assertSessionHas('success');

        $req = ServiceRequest::first();
        $this->assertNotNull($req);
        $this->assertEquals('7307011234560001', $req->nik);
        $this->assertEquals('Ahmad Dahlan', $req->name);
        $this->assertEquals('081234567890', $req->phone);
        // Ticket number format: SRV-YYYYMMDD-XXXXXX
        $this->assertMatchesRegularExpression('/^SRV-\d{8}-[A-F0-9]{6}$/', $req->ticket_number);
    }
}
