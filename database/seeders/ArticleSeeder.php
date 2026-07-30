<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Post;
use App\Models\Announcement;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Membersihkan dan membuat data Kategori, Artikel, dan Pengumuman...');
        
        Category::query()->forceDelete();
        Post::query()->forceDelete();
        Announcement::query()->forceDelete();

        $faker = Faker::create('id_ID');

        // 1. Kategori
        $newsCategories = [
            'Berita Utama', 'Program Desa'
        ];


        foreach ($newsCategories as $name) {
            Category::create(['name' => $name, 'slug' => Str::slug($name)]);
        }

        $newsCatModels = Category::all();

        // 2. Berita (Posts)
        $villageNews = [
            [
                'title' => 'Kerja Bakti Rutin Bersihkan Saluran Irigasi Menjelang Musim Tanam',
                'content' => '<p>Warga desa kembali melaksanakan kegiatan kerja bakti rutin mingguan. Fokus utama kali ini adalah membersihkan saluran irigasi utama yang mengaliri lahan pertanian warga.</p><p>Kepala Desa mengapresiasi antusiasme warga yang hadir. "Kegiatan ini sangat penting untuk memastikan kelancaran pengairan sawah menjelang musim tanam depan," ujarnya.</p><p>Selain membersihkan sampah dan rumput liar, warga juga melakukan pengerukan lumpur yang mengendap agar kapasitas saluran kembali optimal.</p>',
            ],
            [
                'title' => 'Penyaluran Bantuan Langsung Tunai (BLT) Dana Desa',
                'content' => '<p>Pemerintah Desa telah sukses menyalurkan Bantuan Langsung Tunai (BLT) Dana Desa kepada keluarga penerima manfaat (KPM). Acara penyaluran dilakukan di Balai Desa dengan menerapkan antrean tertib.</p><p>Setiap KPM menerima bantuan sesuai dengan ketentuan yang berlaku. Diharapkan bantuan ini dapat meringankan beban ekonomi warga, khususnya dalam memenuhi kebutuhan pokok sehari-hari.</p>',
            ],
            [
                'title' => 'Pelatihan Kewirausahaan untuk Ibu-Ibu PKK: Membuat Kerajinan Daur Ulang',
                'content' => '<p>Tim Penggerak PKK Desa menyelenggarakan pelatihan kewirausahaan dengan tema pemanfaatan limbah rumah tangga. Peserta diajarkan cara membuat kerajinan bernilai ekonomis dari bahan daur ulang seperti plastik dan kertas.</p><p>Kegiatan ini bertujuan untuk meningkatkan keterampilan sekaligus membuka peluang usaha baru bagi ibu-ibu rumah tangga.</p>',
            ],
            [
                'title' => 'Rapat Musyawarah Perencanaan Pembangunan Desa (Musrenbangdes)',
                'content' => '<p>Badan Permusyawaratan Desa (BPD) bersama Pemerintah Desa menyelenggarakan Musrenbangdes untuk membahas prioritas pembangunan di tahun anggaran mendatang. Acara dihadiri oleh perwakilan tokoh masyarakat, pemuda, dan pemuka agama.</p><p>Beberapa usulan utama yang mengemuka antara lain perbaikan jalan usaha tani, pengadaan lampu jalan di titik rawan, serta peningkatan fasilitas kesehatan di Posyandu.</p>',
            ],
            [
                'title' => 'Panen Raya Padi Berjalan Sukses, Petani Sambut Bahagia',
                'content' => '<p>Musim panen raya kali ini membawa kabar gembira bagi para petani di desa kita. Kualitas dan kuantitas hasil panen padi dilaporkan meningkat dibandingkan musim sebelumnya.</p><p>Penyuluh Pertanian Lapangan (PPL) menyatakan bahwa keberhasilan ini tak lepas dari penggunaan bibit unggul dan penerapan pola tanam yang tepat.</p>',
            ],
            [
                'title' => 'Posyandu Balita dan Lansia Kembali Digelar dengan Tingkat Kehadiran Tinggi',
                'content' => '<p>Kegiatan Posyandu bulanan yang diadakan di balai desa berlangsung lancar. Warga sangat antusias membawa balita dan anggota keluarga lanjut usia untuk memeriksakan kesehatan mereka secara gratis.</p><p>Kader Posyandu juga membagikan makanan tambahan bergizi (PMT) untuk menekan angka stunting di desa kita.</p>',
            ],
            [
                'title' => 'Kunjungan Studi Banding dari Desa Tetangga Terkait Pengelolaan BUMDes',
                'content' => '<p>Pemerintah Desa hari ini menerima kunjungan kerja (studi banding) dari perangkat desa tetangga. Kunjungan ini berfokus pada pertukaran ilmu terkait keberhasilan desa kita dalam mengelola Badan Usaha Milik Desa (BUMDes).</p><p>Kepala Desa berharap sinergi antardesa ini dapat terus berlanjut untuk memajukan perekonomian kawasan secara bersama-sama.</p>',
            ]
        ];

        foreach ($villageNews as $news) {
            Post::create([
                'category_id' => $newsCatModels->random()->id,
                'title' => $news['title'],
                'slug' => Str::slug($news['title'] . '-' . Str::random(4)),
                'content' => $news['content'],
                'featured_image' => null, 
                'published_at' => $faker->dateTimeBetween('-6 months', 'now'),
            ]);
        }

        // 3. Pengumuman
        $announcements = [
            [
                'title' => 'Pendaftaran Lomba 17 Agustus 2024 Dibuka',
                'slug' => 'pendaftaran-lomba-17-agustus-2024',
                'content' => '<p>Diberitahukan kepada seluruh warga Desa Tompobulu, pendaftaran perlombaan memperingati HUT RI ke-79 telah dibuka. Perlombaan meliputi tarik tambang, balap karung, dan panjat pinang. Silakan mendaftar ke Ketua RT masing-masing paling lambat tanggal 10 Agustus.</p>',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Jadwal Pemadaman Listrik Sementara',
                'content' => '<p>Berdasarkan informasi dari PLN UP3 setempat, akan dilakukan pemadaman listrik sementara pada hari <strong>Kamis, 15 September</strong> mulai pukul 09.00 s/d 14.00 WIB untuk keperluan pemeliharaan jaringan distribusi. Harap warga bersiap.</p>',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Kerja Bakti Massal Sambut Musim Hujan',
                'content' => '<p>Dihimbau kepada seluruh Kepala Keluarga untuk mengikuti kegiatan Kerja Bakti Massal membersihkan selokan dan drainase lingkungan pada hari <strong>Minggu, 20 Oktober</strong> mulai pukul 07.00 WIB. Titik kumpul di balai dusun masing-masing.</p>',
                'published_at' => now()->subDays(10),
            ],
        ];

        foreach ($announcements as $announcement) {
            $announcement['slug'] = Str::slug($announcement['title'] . '-' . Str::random(4));
            Announcement::create($announcement);
        }

        $this->command->info('Berhasil membuat 2 kategori, 7 berita, dan 3 pengumuman.');
    }
}
