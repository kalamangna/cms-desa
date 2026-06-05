<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Post;
use App\Models\Official;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Dataset;
use App\Models\Publication;
use App\Models\BudgetCategory;
use App\Models\BudgetRealization;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Categories
        $newsCat = Category::create(['name' => 'Berita Utama', 'slug' => 'berita-utama']);
        $progCat = Category::create(['name' => 'Program Desa', 'slug' => 'program-desa']);

        // Posts
        Post::create([
            'category_id' => $newsCat->id,
            'title' => 'Desa Tompobulu Raih Penghargaan Desa Cantik Terbaik',
            'slug' => Str::slug('Desa Tompobulu Raih Penghargaan Desa Cantik Terbaik'),
            'content' => '<p>Desa Tompobulu di Kabupaten Sinjai berhasil meraih penghargaan sebagai Desa Cinta Statistik (Desa Cantik) terbaik tingkat nasional tahun 2024. Penghargaan ini diberikan atas komitmen pemerintah desa dalam mengelola data secara mandiri dan transparan.</p>',
            'published_at' => now(),
        ]);

        Post::create([
            'category_id' => $progCat->id,
            'title' => 'Pengembangan Potensi Pertanian Desa Tompobulu',
            'slug' => Str::slug('Pengembangan Potensi Pertanian Desa Tompobulu'),
            'content' => '<p>Pemerintah Desa bersama masyarakat terus berupaya meningkatkan produktivitas sektor pertanian. Hal ini diharapkan dapat meningkatkan ekonomi lokal melalui inovasi pengolahan hasil tani dan peternakan di kawasan Bulupoddo.</p>',
            'published_at' => now()->subDays(2),
        ]);

        // Officials
        Official::create([
            'name' => 'Andi Muhammad Arif',
            'position' => 'Kepala Desa',
            'nip' => '19800101 201001 1 001',
        ]);

        Official::create([
            'name' => 'H. Syamsul Bahri',
            'position' => 'Sekretaris Desa',
            'nip' => '19850202 201502 1 002',
        ]);

        Official::create([
            'name' => 'Sitti Aminah',
            'position' => 'Kaur Keuangan',
        ]);

        // Announcements
        Announcement::create([
            'title' => 'Penyaluran BLT Dana Desa Tahap II',
            'slug' => Str::slug('Penyaluran BLT Dana Desa Tahap II'),
            'content' => 'Diberitahukan kepada seluruh penerima manfaat bahwa penyaluran BLT akan dilaksanakan pada hari Senin depan di Aula Kantor Desa.',
            'published_at' => now(),
        ]);

        // Events
        Event::create([
            'title' => 'Musyawarah Perencanaan Pembangunan Desa (Musrenbangdes)',
            'slug' => Str::slug('Musyawarah Perencanaan Pembangunan Desa Musrenbangdes'),
            'content' => 'Pembahasan rencana pembangunan untuk tahun anggaran mendatang.',
            'location' => 'Aula Kantor Desa',
            'start_at' => now()->addDays(7),
            'end_at' => now()->addDays(7)->addHours(4),
        ]);

        // Datasets
        Dataset::create([
            'title' => 'Data Kependudukan Desa Tompobulu 2023',
            'slug' => Str::slug('Data Kependudukan Desa Tompobulu 2023'),
            'description' => 'Dataset lengkap jumlah penduduk per dusun, jenis kelamin, dan kelompok usia.',
            'year' => 2023,
            'source' => 'Sistem Informasi Desa',
            'file_csv' => '#',
        ]);

        // Publications
        Publication::create([
            'title' => 'Profil Statistik Desa Tompobulu 2024',
            'slug' => Str::slug('Profil Statistik Desa Tompobulu 2024'),
            'type' => 'Profil Statistik',
            'year' => 2024,
            'pdf_file' => '#',
        ]);

        // APBDes
        $pendapatan = BudgetCategory::create(['name' => 'Pendapatan', 'slug' => 'pendapatan']);
        $belanja = BudgetCategory::create(['name' => 'Belanja', 'slug' => 'belanja']);

        BudgetRealization::create([
            'budget_category_id' => $pendapatan->id,
            'title' => 'Dana Desa',
            'year' => date('Y'),
            'budget_amount' => 1200000000,
            'realization_amount' => 900000000,
        ]);

        BudgetRealization::create([
            'budget_category_id' => $belanja->id,
            'title' => 'Pembangunan Infrastruktur',
            'year' => date('Y'),
            'budget_amount' => 800000000,
            'realization_amount' => 500000000,
        ]);
    }
}
