# Changelog - Website Desa Cantik

Semua perubahan signifikan pada proyek ini akan didokumentasikan di file ini.

## [1.3.0] - 2026-07-01

### Added
- **Modul Layanan (Services)**: Membuat tabel `services`, model `Service`, dan Filament `ServiceResource` untuk mengelola layanan masyarakat secara dinamis dari admin panel.
- **Seeder Layanan Default**: Menginisialisasi 6 layanan dasar (KK, KTP, Akta, Buku Nikah, Domisili, SKTM) lengkap dengan detail persyaratan dalam `DefaultDataSeeder.php`.
- **Galeri Video (YouTube Embed)**: Menambahkan kolom `youtube_url` pada tabel `galleries`, model `Gallery`, dan `GalleryForm.php` admin untuk mendukung video di galeri desa.
- **Modul Wilayah Dusun**: Membuat tabel `dusuns`, model `Dusun`, dan Filament `DusunResource` di bawah menu Master untuk mengelola pembagian wilayah dusun dan nama kepala dusun.

### Changed
- **Pembaruan Halaman Layanan**: Menghubungkan halaman `/layanan` ke database, meloop data layanan, dan membuat modal overlay interaktif dengan AlpineJS untuk menampilkan persyaratan secara instan.
- **Pengembangan Pengaturan Desa**: Menambahkan tab baru "Profil & Sejarah" dan "Media Sosial" di `ManageSettings.php` untuk mengelola data profil desa dan tautan media sosial langsung dari admin.
- **Integrasi Peta & Medsos**: Menghubungkan data koordinat peta dan tautan medsos di tata letak global (`layouts/app.blade.php`, `pages/kontak.blade.php`, dan peta beranda) agar terintegrasi dinamis dengan pengaturan backend.
- **Halaman Galeri dengan Pemutar YouTube**: Mendeteksi tautan YouTube di halaman depan (`/galeri`) untuk menampilkan tombol "Play Video" secara visual dan merender *iframe* YouTube langsung di dalam modal lightbox saat diperbesar.
- **Relasi Penduduk per Dusun**: Menghubungkan model `Citizen` dengan `Dusun` (relasi `belongsTo`), menambahkan dropdown pilihan Dusun di formulir warga admin (`CitizenForm.php`), menampilkan asal Dusun di tabel list warga, serta menghitung jumlah dusun secara dinamis pada statistik beranda.

## [1.2.0] - 2026-07-01

### Added
- **DefaultDataSeeder**: Konsolidasi seluruh data dasar struktural (peran/role, pengguna super admin default `kalamangna`, data kunci pengaturan, serta kategori & indikator statistik dasar) ke dalam satu seeder mandiri.
- **Konfigurasi Zona Waktu WITA**: Menetapkan default zona waktu ke `Asia/Makassar` (WITA) baik di berkas konfigurasi `config/app.php` maupun berkas `.env` / `.env.example`.

### Changed
- **Penyederhanaan Seeder**: Menyederhanakan `DatabaseSeeder` agar hanya memanggil `DefaultDataSeeder` dan secara opsional memanggil `SampleDataSeeder` jika variabel lingkungan `SEED_SAMPLE_DATA` bernilai `true`.
- **Relokasi Data Nilai Statistik Sampel**: Memindahkan data angka historis/statistik demo dari seeder utama ke dalam `SampleDataSeeder` agar database bersih pada instalasi produksi.
- **Pembaruan Instruksi GEMINI.md**: Menyesuaikan judul proyek menjadi "Website Desa Cantik (Cinta Statistik)" beserta tumpukan teknologi (Laravel 12, Filament v4, Tailwind v4).
- **Keamanan Booting AppServiceProvider**: Membungkus pengecekan tabel `settings` dalam blok `try-catch` agar tidak merusak perintah CLI/Composer (`composer install`, `php artisan`) saat koneksi database belum dikonfigurasi.

### Removed
- **Redundansi Konfigurasi .env**: Menghapus variabel lingkungan kredensial admin dan identitas desa dari berkas `.env` dan `.env.example` karena telah dikelola dengan aman di dalam seeder dasar.
- **Berkas Seeder Usang**: Menghapus `RolesAndPermissionsSeeder.php`, `SettingSeeder.php`, dan `StatisticDataSeeder.php` karena fungsinya telah digantikan sepenuhnya oleh `DefaultDataSeeder.php`.

## [1.1.0] - 2026-06-05

### Added
- **Tompobulu Pilot Project**: Integrasi *seeder* khusus berisi data struktur asli profil pemerintahan Desa Tompobulu, Bulupoddo, Sinjai (Dipimpin Asri S.).
- **Pengaturan (Settings) Mutakhir**: Merombak Model CRUD konvensional menjadi *Custom Filament Page* yang menggunakan pola Tab Menu serta mengunci akses penambahan dan penghapusan kata kunci untuk menghindari kerusakan sistem (*Immutable Keys*).

### Changed
- **UX Bahasa Indonesia Admin Panel**: Menerjemahkan dan menyederhanakan secara massal (30+ *file*) seluruh nama kolom dan isian formulir di Panel Admin (Filament) dari bahasa Inggris ke bahasa Indonesia yang ringkas dan padat.
- **Struktur Pejabat (Officials)**: Menghapus kolom `nip`, `nik`, `period_start`, dan `period_end` untuk mengakomodir regulasi Perangkat Desa Non-ASN.
- **Kompatibilitas Filament v4**: Migrasi arsitektur deklarasi komponen (dari kelas `Form` ke `Schema`, dan `->schema()` ke `->components()`) untuk kompatibilitas penuh dengan versi Filament terbaru.

### Removed
- **Master Data Redundan**: Memusnahkan tabel `villages` dan `districts` mengingat aplikasi telah mengadopsi model *deployment single-tenant* per Git *repository*.
- **Sample Data Kedaluwarsa**: Menonaktifkan `SampleDataSeeder` lama yang sudah tidak relevan dengan struktur tabel terbaru.

## [1.0.0] - 2024-06-04

### Added
- **Arsitektur Utama**: Inisialisasi Laravel 11 dengan Filament Admin 4.
- **Frontend Modern**: Desain portal berbasis Tailwind CSS 4 dengan sistem desain Emerald & Sky yang elegan.
- **Homepage Dinamis**:
    - Hero section dengan tagline "Desa Cantik" dan visualisasi data utama.
    - Statistik Utama: Penduduk, Dusun, UMKM, dan Luas Wilayah (terintegrasi database).
    - Dashboard Preview: Grafik distribusi pekerjaan dan pendidikan (Chart.js).
    - Transparansi APBDes: Kartu ringkasan anggaran dan grafik alokasi belanja.
    - Peta Interaktif: Integrasi Leaflet.js dengan koordinat dinamis dari pengaturan desa.
    - Galeri Kegiatan: Layout Masonry yang dinamis.
- **Halaman Pemerintahan**: Profil pejabat desa dengan desain kartu modern.
- **Portal Berita**: Layout "1 Featured + 6 Recent" dengan fitur kategori dan pencarian.
- **Dashboard Statistik**: Halaman pusat data lengkap dengan visualisasi tren kependudukan dan sosial.
- **Open Data & Publikasi**: Katalog dataset dan dokumen digital (PDF) yang siap unduh.
- **Backend Admin**: Resource Filament lengkap untuk mengelola semua konten frontend.

### Changed
- **Ikonografi Global**: Migrasi dari Heroicons ke FontAwesome 6 Pro untuk tampilan yang lebih premium dan variatif.
- **SEO & Social Sharing**: Meta tags dinamis pada layout utama untuk optimasi pratinjau link di media sosial.
- **Paginasi**: Kustomisasi tampilan paginasi Laravel agar selaras dengan desain Tailwind UI aplikasi.

### Optimized
- **Performa**: Implementasi mechanism *Query Caching* pada HomeController untuk meningkatkan kecepatan akses halaman depan.
- **Dinamisasi Konten**: Seluruh teks sambutan, judul, dan koordinat peta kini ditarik dari tabel `Settings`.
- **Database**: Migrasi konfigurasi dari SQLite ke MySQL untuk kesiapan deployment produksi.

---
*Dikembangkan dengan ❤️ untuk Program Desa Cantik BPS.*
