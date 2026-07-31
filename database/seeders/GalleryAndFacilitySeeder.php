<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gallery;
use App\Models\VillagePotential;
use App\Models\PublicFacility;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class GalleryAndFacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membersihkan dan membuat data modul tambahan (Galeri, Potensi, Fasilitas)...');
        
        // Membersihkan data lama
        Gallery::query()->forceDelete();
        VillagePotential::query()->forceDelete();
        PublicFacility::query()->forceDelete();

        $faker = Faker::create('id_ID');

        // 1. Galeri
        $galleries = [
            ['title' => 'Kegiatan Gotong Royong Warga', 'type' => 'foto', 'description' => 'Kegiatan rutin gotong royong membersihkan lingkungan desa setiap akhir pekan.'],
            ['title' => 'Peresmian Balai Desa Baru', 'type' => 'foto', 'description' => 'Acara peresmian balai desa yang dihadiri oleh Bupati dan aparatur desa.'],
            ['title' => 'Panen Raya Padi', 'type' => 'foto', 'description' => 'Masyarakat merayakan panen raya padi dengan hasil yang melimpah tahun ini.'],
            ['title' => 'Profil Desa (Video)', 'type' => 'video', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'description' => 'Video dokumenter singkat mengenai profil dan potensi alam desa.'],
            ['title' => 'Lomba 17 Agustus', 'type' => 'foto', 'description' => 'Kemeriahan peringatan hari kemerdekaan dengan berbagai lomba tradisional.'],
            ['title' => 'Penyaluran Bantuan Sosial', 'type' => 'foto', 'description' => 'Pembagian sembako kepada warga kurang mampu di balai desa.'],
            ['title' => 'Pelatihan UMKM Desa', 'type' => 'foto', 'description' => 'Workshop peningkatan kualitas produk lokal untuk ibu-ibu PKK.'],
            ['title' => 'Posyandu Balita & Lansia', 'type' => 'foto', 'description' => 'Kegiatan cek kesehatan gratis yang diadakan setiap bulan di setiap dusun.'],
            ['title' => 'Pembangunan Jalan Desa', 'type' => 'foto', 'description' => 'Proyek betonisasi jalan penghubung antardusun menggunakan dana desa.'],
            ['title' => 'Pesta Panen Mappadendang', 'type' => 'foto', 'description' => 'Upacara adat syukuran atas hasil bumi yang dirayakan setiap tahun setelah panen.'],
            ['title' => 'Wisata Air Terjun Batu Barae (Video)', 'type' => 'video', 'youtube_url' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw', 'description' => 'Jelajah pesona keindahan air terjun dan alam sekitar desa.'],
            ['title' => 'Pasar Tradisional Desa', 'type' => 'foto', 'description' => 'Aktivitas jual beli warga di pasar desa yang buka setiap hari Minggu.'],
        ];

        foreach ($galleries as $g) {
            $g['slug'] = Str::slug($g['title']);
            Gallery::create($g);
        }

        // 3. Potensi Desa
        $potentials = [
            [
                'title' => 'Pertanian Padi Organik',
                'category' => 'Pertanian & Perkebunan',
                'description' => '<p>Desa memiliki lahan pertanian seluas ratusan hektar yang kini mulai difokuskan pada penanaman padi organik yang ramah lingkungan dan bernilai jual tinggi.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Wisata Air Terjun Batu Barae',
                'category' => 'Pariwisata',
                'description' => '<p>Destinasi wisata alam yang menawarkan keindahan air terjun bertingkat yang asri, sangat berpotensi meningkatkan PADes (Pendapatan Asli Desa).</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Kerajinan Anyaman Bambu',
                'category' => 'UMKM',
                'description' => '<p>Produk unggulan UMKM desa berupa kerajinan perabotan rumah tangga berbahan dasar bambu yang sudah menembus pasar nasional.</p>',
                'is_active' => true,
            ],
        ];

        foreach ($potentials as $p) {
            $p['slug'] = Str::slug($p['title']);
            VillagePotential::create($p);
        }

        // 4. Fasilitas Umum
        $facilities = [
            ['name' => 'Masjid Jami Al-Hidayah', 'type' => 'Tempat Ibadah', 'latitude' => '-6.595', 'longitude' => '106.789', 'address' => 'Jl. Raya Desa No. 10', 'description' => 'Masjid utama desa untuk kegiatan keagamaan besar.'],
            ['name' => 'SD Negeri 1 Tompobulu', 'type' => 'Pendidikan', 'latitude' => '-6.596', 'longitude' => '106.790', 'address' => 'Jl. Pendidikan No. 2', 'description' => 'Sekolah dasar rujukan dengan fasilitas perpustakaan lengkap.'],
            ['name' => 'Puskesmas Pembantu (Pustu)', 'type' => 'Kesehatan', 'latitude' => '-6.597', 'longitude' => '106.788', 'address' => 'Jl. Kesehatan No. 5', 'description' => 'Pusat layanan kesehatan pertama bagi masyarakat desa.'],
            ['name' => 'Lapangan Olahraga', 'type' => 'Olahraga', 'latitude' => '-6.594', 'longitude' => '106.791', 'address' => 'Gg. Lapangan Utama', 'description' => 'Fasilitas olahraga terbuka untuk sepakbola dan voli.'],
        ];

        foreach ($facilities as $f) {
            PublicFacility::create($f);
        }

        $this->command->info('Berhasil menyuntikkan data Galeri, Potensi Desa, dan Fasilitas Umum.');
    }
}
