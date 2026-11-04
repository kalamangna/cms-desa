<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\URL;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Tests\TestCase;

class LivewirePreviewTest extends TestCase
{
    public function test_livewire_preview_file_returns_inline_disposition(): void
    {
        $storage = FileUploadConfiguration::storage();
        $dummyImage = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $filename = 'test-inline-check.png';
        $storage->put('livewire-tmp/'.$filename, $dummyImage);

        try {
            $url = URL::temporarySignedRoute('livewire.preview-file', now()->addMinutes(30), ['filename' => $filename]);

            $response = $this->get($url);

            $response->assertStatus(200);
            $disposition = $response->headers->get('Content-Disposition');
            $this->assertNotNull($disposition);
            $this->assertStringContainsString('inline', $disposition);
            $this->assertStringNotContainsString('attachment', $disposition);
        } finally {
            $storage->delete('livewire-tmp/'.$filename);
        }
    }
}
