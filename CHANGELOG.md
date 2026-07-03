# Changelog - Website Desa Cantik

Semua perubahan signifikan pada proyek ini akan didokumentasikan di file ini.

## [1.6.15] - 2026-07-03

### Added
- **Dropdown Cascading Wilayah Administratif**: Mengubah input teks Provinsi, Kabupaten, dan Kecamatan di form "Profil Desa" menjadi dropdown Select dinamis berjenjang (cascading) terintegrasi dengan API Wilayah Indonesia. Form mewajibkan pemilihan Provinsi terlebih dahulu sebelum Kabupaten dan Kecamatan aktif, serta menyimpan nama wilayah berupa teks untuk keselarasan tampilan halaman depan.
- **Cache Data Wilayah**: Mengintegrasikan Laravel Cache (durasi 24 jam) untuk seluruh request data wilayah dari API guna mengoptimalkan kecepatan akses dan performa load halaman admin.

## [1.6.14] - 2026-07-03

### Changed
- **Media Sosial Opsional Tanpa Validasi URL Ketat**: Menghapus aturan validasi `->url()` pada kolom Facebook, Instagram, dan YouTube di halaman "Pengaturan" agar pengguna dapat memasukkan teks kosong, karakter `#`, atau username tanpa diblokir oleh format URL protokol standar (`http://` atau `https://`).

## [1.6.13] - 2026-07-03

### Changed
- **Pengaturan Semua Opsional (Optional Settings)**: Menghapus aturan wajib diisi (`required()`) pada seluruh kolom di halaman "Profil Desa" (Kecamatan, Kabupaten, Provinsi) dan halaman "Pengaturan" (Nama Desa, Kepala Desa) agar pengguna dapat menyimpan pengaturan secara bebas tanpa hambatan validasi.

## [1.6.12] - 2026-07-03

### Changed
- **Pilihan Dusun Wajib (Required)**: Mengubah kolom dropdown pemilihan Dusun pada form import Keluarga dan Penduduk menjadi wajib diisi (`required()`) sebelum mengunggah berkas Excel/CSV guna memastikan seluruh data yang masuk terpetakan ke Dusun dengan benar.

## [1.6.11] - 2026-07-03

### Fixed
- **Perbaikan ArgumentCountError pada Import Keluarga**: Memperbaiki pemanggilan fungsi `findColumnIndex` pada variabel penentu kolom sumber air (`$colWater`) di `ListFamilies.php` agar menyertakan parameter `$header` yang sebelumnya terlewat.

## [1.6.10] - 2026-07-03

### Fixed
- **Keandalan List Bullet & Numbering**: Menambahkan aturan display `list-item` pada `li`, model `outside` pada marker `ol`/`ul`, dan merombak tag `<p>` di dalam list item menjadi `inline` agar penomoran angka/peluru list yang disimpan oleh editor teks Filament muncul secara konsisten 100% di semua browser.

## [1.6.9] - 2026-07-03

### Fixed
- **Penomoran Misi Desa (Numbering)**: Menyediakan style `.prose` kustom di `app.css` untuk menangani list terurut (`<ol>`) dan list biasa (`<ul>`), serta memperbarui fallback data dan seeder agar menggunakan tag `<ol>` untuk penomoran otomatis yang rapi di profil desa.
- **Rendering Sambutan Kepala Desa**: Mengubah tag render `{{ ... }}` menjadi `{!! ... !!}` pada `home.blade.php` untuk menampilkan teks sambutan Kades yang kaya format HTML secara utuh tanpa meloloskan tag HTML mentah (seperti `<p>`).

## [1.6.8] - 2026-07-02

### Changed
- **Peningkatan Keandalan Validasi Berkas**: Memperketat pencocokan header kolom (misal `'101. nama kepala keluarga'`, `'201. jenis bangunan'`, dan `'306. jenis kelamin'`) pada validasi file Keluarga dan Penduduk agar deteksi salah upload bekerja 100% akurat tanpa false-positive akibat kemiripan kata kunci parsial (seperti substring `'nik'`).

## [1.6.7] - 2026-07-02

### Changed
- **Pembersihan Resource Stream**: Menghapus sisa kode pemanggilan `fclose()` pada akhir proses pembacaan file import di `ListCitizens.php` dan `ListFamilies.php` untuk mencegah terjadinya `TypeError` setelah migrasi parser ke PhpSpreadsheet.

## [1.6.6] - 2026-07-02

### Added
- **Deteksi & Proteksi Salah Upload File**: Menambahkan sistem deteksi headers file cerdas pada modul Keluarga dan Penduduk untuk mendeteksi apabila berkas tertukar (misal file Keluarga diunggah di form Penduduk atau sebaliknya). Sistem akan langsung menghentikan proses import dan menampilkan notifikasi kesalahan yang persisten.

## [1.6.5] - 2026-07-02

### Added
- **Pilihan Dusun Sebelum Import**: Menambahkan form dropdown pilihan Dusun (opsional) sebelum mengunggah file Excel/CSV pada modul Keluarga dan Penduduk, yang mana jika dipilih akan secara otomatis menetapkan Dusun tersebut ke seluruh record data yang di-import.

## [1.6.4] - 2026-07-02

### Added
- **Dukungan Import File Excel (.xlsx / .xls)**: Menginstal paket `phpoffice/phpspreadsheet` dan merombak proses import data kependudukan pada `ListCitizens.php` (Penduduk) dan `ListFamilies.php` (Keluarga) agar dapat membaca file Excel secara langsung di samping file CSV.

## [1.6.3] - 2026-07-02

### Changed
- **Perbaikan Namespace Komponen Grid Filament v4**: Memperbaiki rujukan namespace komponen `Grid` dari `Filament\Forms\Components` ke `Filament\Schemas\Components` pada `FamilyForm.php` dan `CitizenForm.php` agar kompatibel dengan sistem layouting Filament v4.

## [1.6.2] - 2026-07-02

### Changed
- **Perbaikan Namespace Komponen Tabs Filament v4**: Memperbaiki rujukan namespace komponen `Tabs` dan `Tab` dari `Filament\Forms\Components` ke `Filament\Schemas\Components` pada `FamilyForm.php` dan `CitizenForm.php` agar kompatibel dengan sistem schema Filament v4.

## [1.6.1] - 2026-07-02

### Changed
- **Penyederhanaan Kolom Footer**: Menghapus kolom **Data & Layanan** dari footer layout utama, lalu menyesuaikan tata letak grid kembali menjadi 5 kolom agar seimbang dengan proporsi lebar: Brand (2), Tautan Cepat (1), dan Kontak Kami (2).

## [1.6.0] - 2026-07-02

### Changed
- **Penyederhanaan Halaman Profil Desa**: Merombak total halaman `/profil` dengan menghapus timeline milestone perjalanan desa, menyatukan seluruh seksi informasi (Sejarah, Visi Misi, Karakteristik Wilayah) menjadi satu alur halaman scrollable berkelanjutan, serta membuang sistem navigasi tab Alpine.js.

## [1.5.9] - 2026-07-02

### Changed
- **Pelebaran Kolom Kontak di Footer**: Mengubah grid layout footer dari 5 menjadi 6 kolom di desktop dan memberikan `col-span-2` pada seksi **Kontak Kami** agar data peta (Google Maps) dan detail alamat tampil lebih lebar dan nyaman dibaca.

## [1.5.8] - 2026-07-02

### Changed
- **Perbaikan Query Pengurutan Aparatur**: Menghapus klausa pengurutan berdasarkan kolom `order` yang tidak terdaftar pada tabel `officials` di `OfficialController.php`, menyelesaikan error QueryException 1054 di halaman `/aparatur`.

## [1.5.7] - 2026-07-02

### Changed
- **Pembaruan Dokumentasi README.md**: Merapikan dokumen panduan instalasi, menyelaraskan instruksi deployment Hostinger hPanel dengan bypass symbolic link, menambahkan dokumentasi fitur data mikro baru, dan merinci langkah troubleshooting hak akses folder.

## [1.5.6] - 2026-07-02

### Changed
- **Penerapan Gambar Fallback Lokal**: Mengubah seluruh placeholder dan fallback gambar di semua modul (Berita Utama, Berita Grid, Kepala Desa, Galeri, dan Publikasi) agar menggunakan berkas gambar lokal `/img/meta.png` sebagai pengganti placeholder eksternal Unsplash maupun placeholder blok ikon abu-abu.
- **Perbaikan Rujukan Publikasi**: Memperbaiki bug rujukan atribut `cover_image` menjadi `cover` pada modul Publikasi di halaman Beranda agar gambar cover yang diunggah dapat tampil.

## [1.5.5] - 2026-07-02

### Changed
- **Pembaruan Parameter URL Unsplash**: Menambahkan parameter format, crop, dan kualitas (`auto=format&fit=crop`) pada URL fallback Unsplash di model `Gallery` dan atribut `onerror` di `home.blade.php` agar server Unsplash tidak menolak permintaan dengan status 404.

## [1.5.4] - 2026-07-02

### Changed
- **Penerapan Placeholder Unsplash untuk Galeri**: Mengubah logika model `Gallery` (`getImageUrlAttribute()`) agar menggunakan fallback gambar pemandangan desa dari Unsplash secara langsung jika berkas gambar kosong atau berupa `gallery_dummy.jpg`, guna mengatasi error 403 loading di server hosting Hostinger hPanel.

## [1.5.3] - 2026-07-02

### Changed
- **Penyimpanan Storage Fisik Langsung**: Mengubah root penyimpanan disk `public` langsung ke direktori `public/storage` di `config/filesystems.php` untuk memotong ketergantungan pada symbolic link.
- **Rute Salin Media Rekursif**: Memperbarui rute `/init-link` di `routes/web.php` untuk melakukan penyalinan folder secara rekursif dari `storage/app/public` ke `public/storage` sebagai pengganti fungsi `symlink()` PHP yang diblokir oleh hosting.

## [1.5.2] - 2026-07-02

### Added
- **Rute Inisialisasi Link**: Menambahkan rute `/init-link` untuk memicu fungsi `symlink()` PHP secara programatis guna memintas pemblokiran fungsi `exec()` shell command oleh hosting.

## [1.5.1] - 2026-07-02

### Changed
- **Pembersihan Dashboard Admin**: Menghapus widget kependudukan & pintas cepat kustom dari dashboard admin, mengembalikannya ke template bawaan Filament (`AccountWidget` dan `FilamentInfoWidget`) sesuai permintaan penyederhanaan.
- **Reorganisasi Menu Navigasi**: Memindahkan tautan submenu **Dokumen** dari kategori dropdown **Informasi** ke dalam kategori dropdown **Data** di navbar desktop serta menu drawer mobile agar pengelompokan menu data/unduhan lebih konsisten.
- **Optimasi Watch Vite**: Menyesuaikan parameter watch di `vite.config.js` dengan mematikan polling dan menetapkan ambang stabilitas demi mencegah reload loop tanpa akhir saat mendeteksi perubahan file blade.

## [1.5.0] - 2026-07-02

### Added
- **Dashboard Admin Kustom**: Menambahkan `StatsOverviewWidget` (Total Penduduk, Keluarga, Dusun) dan `QuickActionsWidget` (pintasan Import CSV & Pengaturan) pada halaman dashboard panel admin Filament.

### Changed
- **Perombakan Total Halaman Publik**: Mendesain ulang seluruh 12 halaman publik website dengan tema terang modern bersih, aksen emerald green premium, efek hover, dan mikro-animasi yang konsisten:
  - **Layout Global**: Header navbar kini mendukung efek *glassmorphism blur* saat halaman di-scroll, menu aktif dengan *sliding underline* animasi, dan hamburger mobile dengan rotasi 90°.
  - **Footer**: Diperbarui menjadi multi-kolom (5 kolom: Brand, Tautan Cepat, Data & Layanan, Kontak + peta mini embed Google Maps berwarna desaturasi).
  - **Beranda (`/`)**: Disusun ulang menjadi 6 seksi grid modular premium: Hero fullscreen, Stat Cards floating, Sambutan Kades, Data & APBDes berdampingan, Berita & Pengumuman, Galeri & Publikasi.
  - **Berita (`/berita`)**: Grid 3 kolom, featured post sebagai card hero besar, sidebar kategori.
  - **Detail Berita (`/berita/{slug}`)**: Layout artikel dengan hero featured image blur, prose styling, tombol berbagi (FB, WA, X, Salin Link).
  - **Pengumuman (`/pengumuman`)**: Timeline vertikal dengan accordion Alpine.js expand/collapse per item.
  - **Galeri (`/galeri`)**: Masonry grid dengan filter Foto/Video Alpine.js, badge YouTube merah untuk konten video.
  - **Dokumen (`/dokumen`)**: List card horizontal dengan ikon dinamis per tipe file (PDF/DOC/XLS/PPT/ZIP) dan badge warna.
  - **Publikasi (`/publikasi`)**: Grid 4 kolom dengan cover image aspect 3:4, badge tahun & tipe warna dinamis.
  - **Statistik (`/statistik`)**: Navigasi tab Alpine.js per kategori, Big Stats Card di atas setiap grafik, layout 2 kolom chart.
  - **APBDes (`/apbdes`)**: Summary card 3 kolom, progress bar realisasi per pos, accordion detail per kategori, ring SVG persentase, badge status otomatis.
  - **Profil (`/profil`)**: Tiga tab Alpine.js (Sejarah, Visi Misi, Karakteristik Wilayah) dengan timeline vertikal milestone sejarah.
  - **Aparatur Desa (`/aparatur`)**: Grid kartu 4 kolom, foto circular dengan ring emerald on-hover, badge jabatan.
  - **Layanan (`/layanan`)**: Grid kartu layanan dengan persyaratan collapsible Alpine.js dan tombol ajukan.
  - **Kontak (`/kontak`)**: Split layout info kontak + embed peta Google Maps, tombol WA auto-konversi nomor, ikon sosmed branded.
- **Konvensi URL**: Mengubah route `/pemerintahan` menjadi `/aparatur` agar selaras dengan label menu "Aparatur Desa". Semua referensi di navbar, footer, dan breadcrumb diperbarui secara menyeluruh.

## [1.4.0] - 2026-07-01

### Added
- **Skema & Model Keluarga (Family)**: Menambahkan tabel `families` dan model `Family` untuk menampung data kuesioner profil keluarga (karakteristik bangunan, sanitasi, listrik, dokumentasi foto rumah, serta aset kepemilikan motor/mobil/tanah/dll).
- **Filament FamilyResource**: Menyediakan panel admin khusus Keluarga dengan form multi-tab yang membagi input data sesuai kelompok kuesioner.
- **Import CSV Google Form Kependudukan Mikro**: Menambahkan aksi header "Import CSV GForm" pada halaman list Keluarga dan list Penduduk. Aksi ini secara cerdas mencocokkan kolom pertanyaan Google Form, mem-parsing koordinat RT/RW, dan merelasikan data warga individu ke keluarga secara otomatis.

### Changed
- **Overhaul Model & Form Penduduk (Citizen)**: Memperluas tabel `citizens` dengan 40+ kolom baru (status keberadaan, detail pendapatan bulanan, jenis disabilitas, dan 17 jenis penyakit kronis) serta mendesain ulang form warganya dengan tata letak multi-tab.
- **Otomatisasi Grafik Statistik (/statistik)**: Menghubungkan visualisasi data di `/statistik` agar langsung dihitung secara real-time dari data mikro warga dan keluarga ketika database terisi (jumlah penduduk per gender, grafik jenjang pendidikan, grafik jenis pekerjaan, dan jumlah keluarga prasejahtera/penerima bansos).
- **Sinkronisasi legacy pendidikan**: Menambahkan model boot lifecycle di `Citizen.php` untuk otomatis mensinkronkan kolom `education` dan `education_level`.
- **Pembersihan Data Statistik**: Memangkas kategori statistik lama yang tidak terdapat dalam kuesioner Excel (seperti stunting dan UMKM) serta menambahkan kategori baru hasil kuesioner (seperti disabilitas, penyakit kronis, kepemilikan rumah, dan bantuan sosial) pada database seeder (`DefaultDataSeeder.php`) dan pengontrol statistik.
- **Penyederhanaan Menu Admin**: Menghapus resource Filament manual kustom (`StatisticCategoryResource`, `StatisticIndicatorResource`, dan `StatisticDataResource`) dari panel admin karena perhitungan statistik kini telah terotomatisasi penuh secara real-time dari data warga dan keluarga.
- **Sistem Auto-Slug Otomatis**: Membuat trait `HasSlug` untuk mengotomatisasi pembuatan slug dari field `title` atau `name` saat model disimpan, serta menghapus seluruh kolom input `slug` dari 9 form admin Filament agar penginputan lebih praktis.
- **Dinamisasi Galeri (Foto & Video)**: Menambahkan kolom `type` pada galeri. Di halaman admin, form input galeri sekarang adaptif: menampilkan unggahan file gambar jika memilih tipe 'Foto', atau kolom tautan YouTube jika memilih tipe 'Video'. Halaman publik (`/galeri`) secara otomatis memuat thumbnail video dari YouTube jika admin mengunggah video tanpa gambar cover.
- **Pemisahan Modul Profil & Sejarah**: Membuat halaman kustom baru `ManageProfile` (`Profil & Sejarah`) di bawah grup baru `Profil Desa` di sidebar, serta menghapus tab tersebut dari halaman Pengaturan Aplikasi agar navigasi lebih terfokus.
- **Penyusunan Ulang Navigasi Sidebar**: Merestrukturisasi kelompok menu admin menjadi 6 grup logis (Kependudukan, Profil Desa, Informasi, Keuangan & Data, Master, Sistem) serta menyelaraskan urutan sortasi (*navigationSort*) masing-masing menu agar antarmuka admin terasa premium dan intuitif.
- **Pembaruan Lokasi Wilayah Dusun & Karakteristik**: Memindahkan menu `Wilayah Dusun` ke grup `Master` (urutan 5), serta memindahkan form pengaturan `Karakteristik & Wilayah` dari Pengaturan Aplikasi ke dalam tab halaman `Profil & Sejarah` di bawah grup `Profil Desa`.
- **Penghapusan Modul Metadata**: Menghapus modul `MetadataResource` dari panel admin Filament sesuai permintaan penyederhanaan data.
- **Pembersihan Cache & Auto-Publish**: Menambahkan pembersihan cache otomatis di beranda (seperti `home_posts`, `home_announcements`, dll.) pada event saving/deleted model agar postingan baru langsung tayang tanpa menunggu 1 jam. Juga mengotomatiskan pengisian `published_at` ke waktu sekarang (`now()`) jika admin mengosongkannya, agar berita yang baru dibuat langsung tampil di halaman publik.
- **Penyelarasan Menu Halaman Publik & Admin**: Mengubah label menu "Perangkat" di halaman publik (navbar & halaman tim) menjadi "Aparatur Desa" agar konsisten dengan admin, serta menyederhanakan nama grup navigasi admin "Keuangan & Data" menjadi "Data" agar selaras dengan menu dropdown di halaman publik.

## [1.3.0] - 2026-07-01

### Added
- **Modul Layanan (Services)**: Membuat tabel `services`, model `Service`, dan Filament `ServiceResource` untuk mengelola layanan masyarakat secara dinamis dari admin panel.
- **Seeder Layanan Default**: Menginisialisasi 6 layanan dasar (KK, KTP, Akta, Buku Nikah, Domisili, SKTM) lengkap dengan detail persyaratan dalam `DefaultDataSeeder.php`.
- **Galeri Video (YouTube Embed)**: Menambahkan kolom `youtube_url` pada tabel `galleries`, model `Gallery`, dan `GalleryForm.php` admin untuk mendukung video di galeri desa.
- **Modul Wilayah Dusun**: Membuat tabel `dusuns`, model `Dusun`, dan Filament `DusunResource` di bawah menu Master untuk mengelola pembagian wilayah dusun dan nama kepala dusun.
- **Fitur Import CSV Data Statistik**: Menyediakan tombol header "Import CSV" di halaman data statistik admin (`ListStatisticData.php`) untuk memproses unggahan file CSV (hasil Google Forms/Excel) ke dalam database secara otomatis (mendukung deteksi kolom dinamis bilingual).

### Changed
- **Pembaruan Halaman Layanan**: Menghubungkan halaman `/layanan` ke database, meloop data layanan, dan membuat modal overlay interaktif dengan AlpineJS untuk menampilkan persyaratan secara instan.
- **Pengembangan Pengaturan Desa**: Menambahkan tab baru "Profil & Sejarah" dan "Media Sosial" di `ManageSettings.php` untuk mengelola data profil desa dan tautan media sosial langsung dari admin.
- **Integrasi Peta & Medsos**: Menghubungkan data koordinat peta dan tautan medsos di tata letak global (`layouts/app.blade.php`, `pages/kontak.blade.php`, dan peta beranda) agar terintegrasi dinamis dengan pengaturan backend.
- **Halaman Galeri dengan Pemutar YouTube**: Mendeteksi tautan YouTube di halaman depan (`/galeri`) untuk menampilkan tombol "Play Video" secara visual dan merender *iframe* YouTube langsung di dalam modal lightbox saat diperbesar.
- **Relasi Penduduk per Dusun**: Menghubungkan model `Citizen` dengan `Dusun` (relasi `belongsTo`), menambahkan dropdown pilihan Dusun di formulir warga admin (`CitizenForm.php`), menampilkan asal Dusun di tabel list warga, serta menghitung jumlah dusun secara dinamis pada statistik beranda.
- **Pewarnaan Grafik Dinamis**: Menerapkan generator palet warna dinamis (10 warna premium) pada grafik data statistik (`statistics/index.blade.php`) dan diagram lingkaran anggaran belanja (`home.blade.php`) agar setiap kategori/indikator memiliki warna unik meskipun jumlah datanya bertambah.
- **Format dan Unduhan Open Data**: Memperbaiki pencarian dataset di `/dataset` agar dinamis menyaring berdasarkan kata kunci, serta menyelaraskan tampilan format dokumen menjadi tombol unduh mandiri dinamis untuk CSV, XLSX, dan PDF sesuai dengan file yang diunggah di admin panel.

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
