<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Post;
use App\Models\Official;
use App\Models\Announcement;
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
        $newsCat = Category::firstOrCreate(['slug' => 'berita-utama'], ['name' => 'Berita Utama']);
        $progCat = Category::firstOrCreate(['slug' => 'program-desa'], ['name' => 'Program Desa']);

        // Posts
        Post::updateOrCreate(
            ['slug' => Str::slug('Desa Meraih Penghargaan Keterbukaan Informasi Publik')],
            [
                'category_id' => $newsCat->id,
                'title' => 'Desa Meraih Penghargaan Keterbukaan Informasi Publik',
                'content' => '<p>Pemerintah Desa berhasil meraih penghargaan dalam hal keterbukaan informasi dan tata kelola statistik yang transparan. Ini merupakan buah manis kerja sama seluruh perangkat desa dan warga.</p>',
                'published_at' => now(),
            ]
        );

        Post::updateOrCreate(
            ['slug' => Str::slug('Program Padat Karya Tunai Desa Resmi Berjalan')],
            [
                'category_id' => $progCat->id,
                'title' => 'Program Padat Karya Tunai Desa Resmi Berjalan',
                'content' => '<p>Pemerintah Desa bersama masyarakat bahu-membahu memperbaiki saluran irigasi dan jalan tani melalui skema Padat Karya Tunai untuk memajukan perekonomian lokal.</p>',
                'published_at' => now()->subDays(2),
            ]
        );

        // Officials (No NIP as per user requirement)
        Official::updateOrCreate(
            ['name' => 'Syaiful Rijal'],
            ['position' => 'Kepala Desa']
        );

        Official::updateOrCreate(
            ['name' => 'H. Syamsul Bahri'],
            ['position' => 'Sekretaris Desa']
        );

        Official::updateOrCreate(
            ['name' => 'Sitti Aminah'],
            ['position' => 'Kaur Keuangan']
        );

        // Announcements
        Announcement::updateOrCreate(
            ['slug' => Str::slug('Penyaluran BLT Dana Desa Tahap II')],
            [
                'title' => 'Penyaluran BLT Dana Desa Tahap II',
                'content' => 'Diberitahukan kepada seluruh penerima manfaat bahwa penyaluran BLT akan dilaksanakan pada hari Senin depan di Aula Kantor Desa.',
                'published_at' => now(),
            ]
        );

    

        // Documents
        \App\Models\Document::updateOrCreate(
            ['slug' => Str::slug('Peraturan Desa No 1 Tahun 2024 tentang RPJMDes')],
            [
                'title' => 'Peraturan Desa No 1 Tahun 2024 tentang RPJMDes',
                'file' => '#',
            ]
        );

        // Galleries
        \App\Models\Gallery::updateOrCreate(
            ['slug' => Str::slug('Kegiatan Gotong Royong Warga')],
            [
                'title' => 'Kegiatan Gotong Royong Warga',
                'image' => 'settings/gallery_dummy.jpg', // Placeholder
            ]
        );

        // Citizens (Penduduk)
        \App\Models\Citizen::updateOrCreate(
            ['nik' => '7301020304050001'],
            [
                'name' => 'Budi Santoso',
                'address' => 'Dusun I',
                'gender' => 'Laki-laki',
                'date_of_birth' => '1985-05-12',
            ]
        );

        // Datasets
        Dataset::updateOrCreate(
            ['slug' => Str::slug('Data Kependudukan Desa 2023')],
            [
                'title' => 'Data Kependudukan Desa 2023',
                'description' => 'Dataset lengkap jumlah penduduk per dusun, jenis kelamin, dan kelompok usia.',
                'year' => 2023,
                'source' => 'Sistem Informasi Desa',
                'file_csv' => '#',
            ]
        );

        // Publications
        Publication::updateOrCreate(
            ['slug' => Str::slug('Profil Statistik Desa 2024')],
            [
                'title' => 'Profil Statistik Desa 2024',
                'type' => 'Profil Statistik',
                'year' => 2024,
                'pdf_file' => '#',
            ]
        );

        // APBDes
        $pendapatan = BudgetCategory::firstOrCreate(['slug' => 'pendapatan'], ['name' => 'Pendapatan']);
        $belanja = BudgetCategory::firstOrCreate(['slug' => 'belanja'], ['name' => 'Belanja']);

        BudgetRealization::updateOrCreate(
            [
                'budget_category_id' => $pendapatan->id,
                'title' => 'Dana Desa',
                'year' => date('Y'),
            ],
            [
                'budget_amount' => 1200000000,
                'realization_amount' => 900000000,
            ]
        );

        BudgetRealization::updateOrCreate(
            [
                'budget_category_id' => $belanja->id,
                'title' => 'Pembangunan Infrastruktur',
                'year' => date('Y'),
            ],
            [
                'budget_amount' => 800000000,
                'realization_amount' => 500000000,
            ]
        );
    }
}
