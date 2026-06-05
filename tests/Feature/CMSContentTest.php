<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_can_create_event(): void
    {
        Event::create([
            'title' => 'Rapat Desa',
            'slug' => 'rapat-desa',
            'content' => 'Isi rapat',
            'start_at' => now(),
        ]);
        $this->assertDatabaseHas('events', ['title' => 'Rapat Desa']);
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

    public function test_can_create_document(): void
    {
        Document::create([
            'title' => 'Perdes No 1',
            'slug' => 'perdes-no-1',
            'file' => 'perdes.pdf',
        ]);
        $this->assertDatabaseHas('documents', ['title' => 'Perdes No 1']);
    }
}
