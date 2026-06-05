<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TompobuluSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kategori Berita
        $kategoriBudaya = \App\Models\Category::firstOrCreate(['name' => 'Budaya & Adat', 'slug' => 'budaya-adat']);
        $kategoriPemerintahan = \App\Models\Category::firstOrCreate(['name' => 'Pemerintahan', 'slug' => 'pemerintahan']);
        $kategoriWisata = \App\Models\Category::firstOrCreate(['name' => 'Wisata', 'slug' => 'wisata']);

        // Berita
        \App\Models\Post::firstOrCreate(
            ['slug' => 'sejarah-desa-tompobulu-dan-kampung-adat-karampuang'],
            [
                'category_id' => $kategoriBudaya->id,
                'title' => 'Sejarah Desa Tompobulu dan Kampung Adat Karampuang',
                'content' => '<p>Desa Tompobulu adalah desa terluas di Kecamatan Bulupoddo dengan luas sekitar 32,03 km². Wilayahnya merupakan dataran tinggi (103–655 mdpl). Desa ini merupakan hasil pemekaran dari Desa Duampanuae pada tahun 1987. Tompobulu sangat terkenal dengan keberadaan Dusun Karampuang, yang merupakan kampung adat bersejarah peninggalan To Manurung di Batu Lappa.</p>',
                'published_at' => now(),
            ]
        );

        \App\Models\Post::firstOrCreate(
            ['slug' => 'ritual-mappogau-sihanua-agenda-tahunan'],
            [
                'category_id' => $kategoriBudaya->id,
                'title' => 'Ritual Mappogau Sihanua: Agenda Syukuran Tahunan Karampuang',
                'content' => '<p>Masyarakat adat Karampuang di Desa Tompobulu rutin melaksanakan ritual "Mappogau Sihanua", yakni pesta syukuran adat peninggalan leluhur. Sistem kepemimpinan adat dijalankan oleh Ade Eppa (Arung/To Matoa, Gella, Sanro, dan Guru).</p>',
                'published_at' => now()->subDays(2),
            ]
        );

        // Perangkat Desa (Struktur Organisasi)
        \App\Models\Official::firstOrCreate(
            ['name' => 'Asri S.'],
            ['position' => 'Kepala Desa']
        );
        \App\Models\Official::firstOrCreate(
            ['name' => 'Sekretaris Desa Tompobulu'],
            ['position' => 'Sekretaris Desa']
        );
        \App\Models\Official::firstOrCreate(
            ['name' => 'Kasi Pemerintahan'],
            ['position' => 'Kasi Pemerintahan']
        );
        \App\Models\Official::firstOrCreate(
            ['name' => 'Kasi Kesejahteraan'],
            ['position' => 'Kasi Kesejahteraan']
        );
        \App\Models\Official::firstOrCreate(
            ['name' => 'Kepala Dusun Karampuang'],
            ['position' => 'Kepala Dusun Karampuang']
        );

        // Pengumuman
        \App\Models\Announcement::firstOrCreate(
            ['slug' => 'kerja-bakti-persiapan-mappogau-sihanua'],
            [
                'title' => 'Kerja Bakti Persiapan Mappogau Sihanua',
                'content' => 'Diimbau kepada seluruh warga Desa Tompobulu, khususnya di Dusun Karampuang, untuk ikut serta dalam kerja bakti membersihkan area adat.',
            ]
        );
    }
}
