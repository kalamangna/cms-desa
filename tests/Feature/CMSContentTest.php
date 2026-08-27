<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Document;
use App\Models\Gallery;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CMSContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_category(): void
    {
        Category::create(['name' => 'Berita Desa', 'slug' => 'berita-desa']);
        $this->assertDatabaseHas('categories', ['name' => 'Berita Desa']);
    }

    public function test_can_create_post(): void
    {
        $category = Category::create(['name' => 'Berita', 'slug' => 'berita']);
        Post::create([
            'category_id' => $category->id,
            'title' => 'Judul Berita',
            'slug' => 'judul-berita',
            'content' => 'Konten berita',
        ]);
        $this->assertDatabaseHas('posts', ['title' => 'Judul Berita']);
    }

    public function test_can_create_announcement(): void
    {
        Announcement::create([
            'title' => 'Pengumuman Penting',
            'slug' => 'pengumuman-penting',
            'content' => 'Isi pengumuman',
        ]);
        $this->assertDatabaseHas('announcements', ['title' => 'Pengumuman Penting']);
    }

    public function test_can_create_gallery(): void
    {
        Gallery::create([
            'title' => 'Foto Kegiatan',
            'slug' => 'foto-kegiatan',
            'image' => 'foto.jpg',
        ]);
        $this->assertDatabaseHas('galleries', ['title' => 'Foto Kegiatan']);
    }

    public function test_can_create_video_gallery_without_image(): void
    {
        Gallery::create([
            'title' => 'Video Kegiatan',
            'slug' => 'video-kegiatan',
            'type' => 'video',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
        $this->assertDatabaseHas('galleries', [
            'title' => 'Video Kegiatan',
            'type' => 'video',
            'image' => null,
        ]);
    }

    public function test_can_create_document(): void
    {
        Document::create([
            'title' => 'Perdes No 1',
            'slug' => 'perdes-no-1',
            'file' => 'perdes.pdf',
        ]);
        $this->assertDatabaseHas('documents', ['title' => 'Perdes No 1']);
    }

    public function test_portrait_post_image_is_auto_framed_to_16_by_9(): void
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('portrait.jpg', 400, 800);

        $category = Category::create(['name' => 'Berita', 'slug' => 'berita']);
        $post = Post::create([
            'category_id' => $category->id,
            'title' => 'Berita Foto Portrait',
            'slug' => 'berita-foto-portrait',
            'content' => 'Konten berita',
            'featured_image' => $image,
        ]);

        $this->assertNotNull($post->featured_image);
        $this->assertStringEndsWith('.webp', $post->featured_image);

        $storedPath = storage_path('app/public/'.$post->featured_image);
        if (file_exists($storedPath) && function_exists('imagecreatefromwebp')) {
            $img = imagecreatefromwebp($storedPath);
            $w = imagesx($img);
            $h = imagesy($img);
            imagedestroy($img);

            $this->assertGreaterThan($h, $w, 'Lebar gambar hasil harus lebih besar daripada tinggi (16:9)');
            $this->assertEquals(round(16 / 9, 1), round($w / $h, 1));
        }
    }
}
