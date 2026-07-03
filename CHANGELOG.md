# Changelog - Website Desa Cantik

Semua perubahan signifikan pada proyek ini akan didokumentasikan di file ini.

## [1.6.38] - 2026-07-03

### Fixed
- **Perbaikan Query Kategori APBDes Beranda (SQL Column Not Found Hotfix)**: Memperbaiki error `SQLSTATE[42S22]: Column not found` pada query `BudgetRealization::whereHas('category')` di `HomeController.php`. Query dan filter pengelompokan anggaran diperbaiki agar menggunakan kolom `slug` (sebagai ganti kolom `type` yang tidak ada di skema database tabel `budget_categories`).
- **Kompilasi Ulang Aset Produksi (Production Assets Compilation)**: Menjalankan kompilasi aset CSS dan Javascript (`npm run build`) untuk memperbarui file aset dan manifest produksi, menjamin seluruh gaya tampilan layout publik yang responsif terkompilasi dengan benar.
- **Pelebaran Kolom Kontak Kami & Penghapusan Tautan Cepat pada Footer (Footer Layout Refactor)**: Menghapus kolom "Tautan Cepat" pada footer, mengembalikan grid utama desktop menjadi 5 kolom (`lg:grid-cols-5`), dan memperlebar kolom "Kontak Kami" menjadi `lg:col-span-2` agar informasi alamat kantor, email, dan telepon terlihat proporsional, lapang, dan mudah dibaca.
- **Penyelarasan Teks & Ikon Alamat Footer (Footer Address Alignment Fix)**: Menyelaraskan ikon alamat di footer menjadi rata tengah (`items-center`) persis sama seperti perataan ikon email dan nomor HP.
- **Penghapusan Kolom Format & Penamaan Kolom Aksi Tabel Open Data (Open Data Table Refactor)**: Menghapus kolom "Format" yang redundan di tabel halaman Open Data (`datasets/index.blade.php`) karena format file unduhan sudah diwakili oleh tombol aksi, serta mengubah nama header kolom "Aksi" menjadi "Unduh" agar lebih informatif.
- **Penghapusan Tombol Unduh Halaman Statistik (Statistics Download Button Removal)**: Menghapus tombol "Unduh Dataset Riil (CSV)" di bagian bawah tab kategori pada halaman visualisasi statistik (`statistics/index.blade.php`), untuk menghindari duplikasi opsi karena download dataset sudah sepenuhnya diwadahi di halaman Open Data.

## [1.6.39] - 2026-07-04

### Added
- **Peningkatan Komprehensif SEO Teknis (Comprehensive SEO Enhancement)**:
  - Menambahkan `<link rel="canonical">` di layout utama yang bisa di-override per halaman untuk mencegah *duplicate content*.
  - Menambahkan `<meta name="robots" content="index, follow">` secara global.
  - Menjadikan `og:type` dinamis (bisa di-*override* per halaman via `@section('og_type')`), sehingga halaman berita detail sekarang menggunakan `og:type="article"`.
  - Menambahkan `og:site_name`, `og:locale`, `og:image:width`, `og:image:height` pada Open Graph.
  - Menambahkan `@stack('og_extra')` dan `@stack('head')` agar halaman child bisa meng-*inject* meta article dan JSON-LD tambahan.
  - Menambahkan `meta_keywords` yang bisa di-*override* per halaman.
- **JSON-LD Structured Data (Schema.org)**:
  - Menambahkan JSON-LD `Organization` dan `WebSite` (dengan `SearchAction`) secara global di layout utama.
  - Menambahkan JSON-LD `Article` schema di halaman detail berita (`posts/show.blade.php`).
  - Menambahkan `article:published_time`, `article:modified_time`, `article:author`, `article:section` meta tags di halaman berita detail.
  - **BreadcrumbList JSON-LD Otomatis (Dynamic Breadcrumb Schema)**: Menambahkan skema breadcrumb terstruktur secara otomatis di layout utama `layouts/app.blade.php` berbasis segmen URL/route untuk melengkapi navigasi pencarian yang ramah SEO.
- **Upgrade Sitemap XML (`sitemap.blade.php` & `SitemapController.php`)**:
  - Menambahkan semua rute publik ke sitemap: `/profil`, `/layanan`, `/aparatur`, `/kontak`, `/pengumuman`, `/galeri`, `/dokumen`, `/publikasi`, `/dataset`.
  - Menambahkan URL detail pengumuman (`/pengumuman/{slug}`) secara dinamis dari database.
  - Mengatur `changefreq` dan `priority` yang tepat per jenis halaman.
- **Peningkatan `robots.txt`**: Menambahkan blok `Disallow: /admin` dan `Disallow: /admin/*` agar panel admin tidak di-crawl oleh mesin pencari.
- **Minifikasi HTML Output Dinamis (HTML Minification Middleware)**: Membuat middleware `MinifyHtml` yang secara otomatis memangkas spasi/whitespace berlebih dari output HTML pada seluruh halaman publik untuk mereduksi ukuran byte data transfer tanpa merusak skrip atau teks terformat (tag `pre`, `textarea`, `script`, `style`).
- **Lazy Loading Gambar (Image Lazy Loading)**:
  - Menambahkan `loading="lazy"` pada semua tag `<img>` yang berada di bawah fold di `home.blade.php`, `posts/index.blade.php`, dan `posts/show.blade.php`.
  - Gambar LCP (foto kepala desa di hero banner) dikecualikan dari lazy loading.
- **Halaman Detail Pengumuman & Rute Dinamis (Announcement Detail Page & Route)**: Membuat rute `/pengumuman/{slug}`, menambahkan method `show` di `AnnouncementController`, membuat view detail pengumuman `resources/views/announcements/show.blade.php` dengan SEO tag lengkap (meta & JSON-LD), serta memperbarui `index.blade.php` with link ke halaman detail pengumuman.
- **Halaman Error 404 Kustom yang Cantik (Custom 404 Error Page)**: Membuat view kustom `resources/views/errors/404.blade.php` bertema Desa Cantik yang ramah SEO dengan tombol aksi navigasi cepat (Beranda, Berita Utama, Kontak, Layanan, Statistik, dll.) untuk menekan *bounce rate* ketika pengunjung tersesat.

## [1.6.37] - 2026-07-03

### Added
- **Sistem Pelacakan Pengunjung Ter-cache (Visitor Statistics System)**:
  * Membuat tabel database `visitor_logs` dan model `VisitorLog` untuk mencatat pengunjung unik harian (hash sha256 dari kombinasi IP + User Agent) secara anonim.
  * Membuat middleware `TrackVisitor.php` untuk melacak page view halaman publik (melewati AJAX, panel admin, dan rute internal) secara aman.
  * Menggunakan sistem caching data statistik pengunjung harian (hari ini, kemarin, total) selama 5 menit via `AppServiceProvider.php` untuk performa tinggi.
  * Menampilkan badge capsule counter statistik di footer halaman publik sebelah kanan Kontak Kami secara rapi.

### Changed
- **Pembersihan Pengaturan Wilayah & Karakteristik (Region settings cleanup)**:
  * Menghapus input Provinsi, Kabupaten, Kecamatan, dan Populasi dari halaman admin Profil Desa (`ManageProfile.php`) beserta API helper emsifa terkait, memusatkan pengisiannya secara eksklusif pada Pengaturan Umum (`ManageSettings.php`) untuk menghindari redundansi data.
- **Otomatisasi Populasi Wilayah (Dynamic Population Source)**:
  * Mengubah data display populasi pada halaman Profil Publik (`profil.blade.php`) agar dihitung secara dinamis dari database penduduk aktif (`Citizen::where('status', 'Aktif')->count()`) menggantikan input teks statis.
- **Visual Split Hero & Sambutan Beranda (Home Visual Overhaul)**:
  * Mengubah Hero Beranda menjadi format split-screen premium (teks kiri, foto Kepala Desa di kanan).
  * Foto Kepala Desa disesuaikan ukurannya (`w-[270px]` di mobile) agar tampil responsif di semua perangkat termasuk HP 320px tanpa overflow.
  * Merancang ulang Sambutan Kades di beranda menjadi centered blockquote card bermotif tanda kutip transparan untuk menyelesaikan masalah foto kades ganda.
  * Menambahkan tautan klik pada card pengumuman beranda menuju detail pengumuman.
- **Peta Leaflet & OSM Integration**:
  * Menghapus input Google Maps iframe embed, mengintegrasikan peta Leaflet OpenStreetMap secara murni di halaman Kontak menggunakan koordinat Latitude & Longitude database.
- **Arah Jajaran Grid & Padding Adaptif (Mobile Grid & Spacing Adjustments)**:
  * Mengatur heading section beranda (`items-start md:items-end`) agar sejajar rata kiri di mobile dan rata bawah di desktop.
  * Mengatur Stat Cards beranda agar bertumpuk vertikal (`flex-col`) di mobile dengan padding longgar `p-4 sm:p-6` guna mencegah text-clipping.
  * Menyesuaikan vertical padding di semua halaman publik (`py-16 md:py-24 lg:py-28` untuk Hero; `py-16 md:py-20 lg:py-28` untuk Seksi) agar lebih padat dan ergonomis pada layar mobile & tablet.
  * Mengubah grid aparatur desa publik menjadi `grid-cols-1` pada mobile agar tampil satu kolom penuh (*full-width*) yang rapi.
- **Perapian Badge Kartu Publikasi (Publication Card Badges cleanup)**:
  * Memindahkan badge Tahun, Kategori, dan Tipe pada kartu publikasi dari atas cover gambar ke bagian dalam Card Body agar cover buku tidak terhalang teks dan lebih rapi di mobile.
- **Integrasi Tombol TikTok (TikTok Share Button)**:
  * Menghapus tombol bagikan ke X (Twitter) dan menggantinya dengan tombol bagikan ke TikTok dengan branding warna hitam khas TikTok di halaman detail berita.

### Fixed
- **Solusi Schema Namespace & Closure Type-error**:
  * Memperbaiki error `TypeError` Filament v4 dengan menghapus type hint `Get $get` di option closure dan meluruskan penggunaan class Layout `Grid` di `Filament\Schemas\Components\Grid`.
  * Memperbaiki bug z-index Leaflet Map di halaman kontak agar tidak menutupi sticky navbar menu.
  * Memperbaiki wrap judul dokumen di halaman Arsip Dokumen dengan mengganti `truncate` menjadi `break-words` untuk mencegah overflow teks di mobile.

## [1.6.36] - 2026-07-03

### Changed
- **Penyesuaian Judul Section Beranda**: Memperbarui judul bagian grafik di beranda menjadi "Data & Anggaran Desa" agar sesuai dengan isian kolom grafik demografi penduduk dan diagram anggaran APBDes yang disajikan bersandingan.

## [1.6.35] - 2026-07-03

### Fixed
- **Perbaikan Bug Relasi BudgetRealization (Method Call Bug Fix)**: Memperbaiki kesalahan nama metode relasi di `HomeController.php` dari `budgetCategory` menjadi `category` pada model `BudgetRealization`. Ini menyelesaikan error *BadMethodCallException* yang merusak beranda saat memanggil bagan APBDes.

## [1.6.34] - 2026-07-03

### Changed
- **Penyandingan Grafik Kependudukan & Anggaran (Side-by-Side Demographic & Budget charts)**: Mengembalikan layout 2 kolom di halaman beranda (`home.blade.php`) dengan menyandingkan "Grafik Demografi Penduduk" (Laki-laki vs Perempuan) di kolom kiri dan "Grafik & Informasi Realisasi APBDes" di kolom kanan, menggantikan grafik pekerjaan lama.

## [1.6.33] - 2026-07-03

### Changed
- **Penyajian Grafik Kependudukan Tunggal di Beranda**: Menyederhanakan dan memfokuskan layout infografis halaman depan (`home.blade.php`) dengan menghapus panel grafik Pekerjaan dan APBDes, lalu menggantinya dengan "Grafik Demografi Penduduk" (Perbandingan Laki-laki vs Perempuan menggunakan visualisasi Donut Chart premium). Perhitungan diambil secara dinamis ter-cache lewat `HomeController.php`.

## [1.6.32] - 2026-07-03

### Changed
- **Pembersihan Fallback Grafik Pekerjaan di Beranda**: Menghapus fallback label & data dummy pada grafik bar "Distribusi Pekerjaan" di halaman beranda (`home.blade.php`). Grafik beranda sekarang sepenuhnya mematuhi data riil database kependudukan, sehingga bernilai kosong (clean empty chart) jika data warga bernilai 0.

## [1.6.31] - 2026-07-03

### Changed
- **Pembersihan Fallback Data Dummy (Zero-Data Realism)**: Menghapus logika fallback data dummy kependudukan pada `StatisticController.php` dan `HomeController.php`. Halaman statistik dan widget jumlah penduduk di beranda sekarang menyajikan data riil secara murni langsung dari database (menampilkan angka 0 atau grafik kosong jika database kosong) daripada kembali ke nilai placeholder seeder lama.

## [1.6.30] - 2026-07-03

### Added
- **Migrasi Data Wilayah Statis Kabupaten & Provinsi**: Membuat berkas migrasi database baru (`2026_07_03_172711_update_province_and_regency_settings_to_sinjai.php`) untuk memperbarui secara otomatis data pengaturan `province_name` menjadi "SULAWESI SELATAN" dan `regency_name` menjadi "SINJAI" di server produksi saat perintah `php artisan migrate` dijalankan.

## [1.6.29] - 2026-07-03

### Fixed
- **Format Penulisan Tahun Halaman Admin (Thousands Separator Fix)**: Menghapus pemformatan `.numeric()` pada input form dan tabel kolom tahun (`year`) di halaman admin Open Data (`DatasetResource`). Ini menghentikan Filament dari memformat angka tahun secara otomatis dengan tanda pemisah ribuan (titik/koma) seperti "2.026" atau "2,026", sehingga tahun tertulis bersih "2026".

## [1.6.28] - 2026-07-03

### Fixed
- **Alokasi Memori Ekspor PDF (Memory Limit Fix)**: Meningkatkan alokasi memori PHP (`memory_limit` menjadi `512M` dan `set_time_limit` menjadi `120`) di `DatasetController.php` sebelum memproses rendering Dompdf. Ini menyelesaikan masalah *PHP Fatal error: Allowed memory size exhausted* yang menyebabkan browser men-download berkas PDF rusak berisi kode HTML error 500 saat mengompilasi ratusan baris data kependudukan.

## [1.6.27] - 2026-07-03

### Added
- **Ekspor Excel (XLSX) & PDF pada Portal Open Data**: Menambahkan opsi download format Excel (XLSX) menggunakan library PhpSpreadsheet dan format PDF menggunakan Dompdf pada halaman Open Data (`/dataset`). Format unduhan baru ini bersifat dinamis (mengambil data ter-update dari database) dan tetap mematuhi standardisasi privasi total (fully anonymized).

## [1.6.26] - 2026-07-03

### Changed
- **Penyesuaian Deskripsi Dataset Open Data**: Memperbarui deskripsi data kependudukan dan keluarga di database untuk mencerminkan status anonimisasi total (penghapusan NIK, No KK, Nama, dan Alamat Rinci) agar sesuai dengan isi file CSV terbaru yang disajikan.

## [1.6.25] - 2026-07-03

### Changed
- **Anonimisasi Total Data Pribadi Unduhan Open Data (Privacy Enforcement)**: Menghapus seluruh kolom data pribadi teridentifikasi (Nama, NIK, No KK, Alamat Rinci) dari file CSV yang didownload di halaman Open Data (`/dataset`) dan Statistik. Untuk data penduduk, tanggal lahir diubah secara otomatis menjadi kueri umur angka (anonymized age number), dan alamat dipersempit hanya pada cakupan wilayah (RT/RW/Dusun) demi mematuhi UU Pelindungan Data Pribadi (UU PDP).

## [1.6.24] - 2026-07-03

### Removed
- **Panel Open Data di Halaman Statistik**: Menghapus panel visual katalog dataset Open Data di bagian bawah halaman Statistik (`/statistik`) agar halaman statistik tetap bersih dan berfokus penuh pada penyajian diagram/grafik visual. Kemampuan unduh dataset riil secara dinamis pada tiap tab kategori statistik dan di halaman utama Open Data (`/dataset`) tetap dipertahankan.

## [1.6.23] - 2026-07-03

### Added
- **Generator Unduh Dataset Riil Dinamis (Real-time CSV Generator)**: Mengimplementasikan sistem generator unduh dataset CSV secara instan dari database riil untuk data Penduduk dan Keluarga.
- **Route Baru `/dataset/download/{type}`**: Menghubungkan tombol unduh di halaman Open Data (`/dataset`) dan tombol di tab kategori Statistik (`/statistik`) langsung ke generator dinamis tersebut, lengkap dengan penyimaran data privat (sensor NIK & KK menggunakan format mask `730701******0001` demi privasi warga).

## [1.6.22] - 2026-07-03

### Added
- **Integrasi Portal Open Data dengan Statistik**: Menambahkan panel integrasi data dinamis di bagian bawah halaman Statistik (`/statistik`). Halaman ini sekarang mengambil 3 daftar dataset terbuka terbaru dari model `Dataset` dan menampilkannya dalam bentuk kartu interaktif lengkap dengan tombol unduh berkas (CSV, XLSX, PDF) serta tautan navigasi langsung ke Portal Open Data utama.

## [1.6.21] - 2026-07-03

### Added
- **Standardisasi Pemetaan Opsi Kolom (Best Practices)**: Mengintegrasikan helper parsing cerdas (`parseYesNo`, `parseMaritalStatus`, `parseFamilyRelation`, `parseEducationLevel`, `parseJob`, `parseOwnershipStatus`, `parseAssistanceType`) pada skrip import kependudukan di `ListCitizens.php` dan `ListFamilies.php` untuk menormalisasi variasi tulisan tangan respon Google Form secara otomatis, menjamin konsistensi data basis data 100%, serta meminimalisir kegagalan kueri visualisasi statistik.

## [1.6.20] - 2026-07-03

### Fixed
- **Standardisasi Jenis Kelamin (Gender Case Mismatch)**: Menambahkan parser pemetaan cerdas pada proses import warga (`ListCitizens.php`) agar nilai input gender yang beragam ("Laki-Laki", "laki-laki", "l", "perempuan", "p") dipetakan secara seragam ke format enum database ("Laki-laki" / "Perempuan"). Hal ini menyelesaikan bug "jumlah laki laki 0 jiwa" di halaman statistik akibat perbedaan penulisan huruf besar/kecil.
- **Pembersihan Data Eksisting**: Menyamakan data jenis kelamin "Laki-Laki" menjadi "Laki-laki" pada seluruh database lokal agar langsung terbaca di diagram statistik.

## [1.6.19] - 2026-07-03

### Changed
- **Sinkronisasi Otomatis Nama Kepala Desa**: Menghubungkan kolom pengaturan `village_head` di halaman "Pengaturan" agar secara otomatis mengupdate nama Kepala Desa di tabel `officials` (Aparatur Desa), serta menghapus cache `home_village_head` seketika saat disimpan agar perubahan nama langsung tampak di halaman depan tanpa delay.

## [1.6.18] - 2026-07-03

### Changed
- **Nama Provinsi di Beranda & Kapitalisasi Teks (Capitalize/Title Case)**: Menampilkan nama Provinsi secara eksplisit pada bagian lokasi di beranda utama halaman publik, serta menerapkan format penulisan Capitalize (`Str::title`) agar nama Kecamatan, Kabupaten, dan Provinsi tertulis rapi dengan huruf besar di awal kata (contoh: "Kecamatan Bontobahari, Kabupaten Sinjai, Provinsi Sulawesi Selatan").

## [1.6.17] - 2026-07-03

### Changed
- **Pembersihan Otomatis Placeholder Wilayah (Auto-Heal)**: Menambahkan fungsi auto-heal pada method `mount()` di `ManageProfile.php` dan memperbarui `DefaultDataSeeder.php` agar secara otomatis mendeteksi dan memperbarui nilai placeholder database lama ("Nama Provinsi" dan "Nama Kabupaten") menjadi "SULAWESI SELATAN" dan "SINJAI" tanpa memerlukan modifikasi database manual.

## [1.6.16] - 2026-07-03

### Changed
- **Penyederhanaan Wilayah (Dropdown Kecamatan Sinjai)**: Menyederhanakan form Provinsi dan Kabupaten menjadi field teks statis *Read-Only* ("SULAWESI SELATAN" dan "SINJAI"), serta memfokuskan dropdown Kecamatan agar hanya memuat daftar Kecamatan di Kabupaten Sinjai (menggunakan ID Wilayah `7307`) secara dinamis ter-cache dari API untuk mempermudah operasional admin desa.

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
