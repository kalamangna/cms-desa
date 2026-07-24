# 🏛️ Portal Resmi Website Desa

Portal Informasi Desa Modern, Transparan, dan Berbasis Data Mikro. Menyajikan visualisasi data sosial ekonomi (Regsosek/SDGs Desa) secara interaktif.

---

## 📚 Dokumentasi Proyek Per BAB (`docs/`)

Dokumentasi resmi sistem informasi ini disusun secara terstruktur di folder `docs/` di mana setiap BAB memiliki berkas `.md` tersendiri dengan penamaan file berbahasa Inggris:

- 📑 **[TECHNICAL_REPORT.md](docs/TECHNICAL_REPORT.md)** — **Dokumen Laporan Teknis Master Utuh** (Kompilasi Cover s/d BAB X & Lampiran).
- 📜 **[COVER_AND_PREFACE.md](docs/COVER_AND_PREFACE.md)** — Cover, Lembar Pengesahan (Pranata Komputer & Kepala Desa), Kata Pengantar, & Daftar Isi.
- 📘 **[CHAPTER_1_INTRODUCTION.md](docs/CHAPTER_1_INTRODUCTION.md)** — **BAB I: Pendahuluan** (Latar Belakang, Maksud & Tujuan, Ruang Lingkup).
- 📋 **[CHAPTER_2_REQUIREMENTS_ANALYSIS.md](docs/CHAPTER_2_REQUIREMENTS_ANALYSIS.md)** — **BAB II: Analisis Kebutuhan** (Functional & Non-Functional Requirements).
- 📐 **[CHAPTER_3_SYSTEM_DESIGN.md](docs/CHAPTER_3_SYSTEM_DESIGN.md)** — **BAB III: Perancangan Sistem** (Arsitektur MVC & Data Flow Diagram).
- 🛠️ **[CHAPTER_4_IMPLEMENTATION.md](docs/CHAPTER_4_IMPLEMENTATION.md)** — **BAB IV: Implementasi** (Laravel 12, Filament v4, Stacked Bar 2 Arah, Sticky Table).
- 🗄️ **[CHAPTER_5_DATABASE.md](docs/CHAPTER_5_DATABASE.md)** — **BAB V: Database** (Spesifikasi Tabel Utama `citizens`, `families`, `statistic_categories`).
- 🌐 **[CHAPTER_6_API.md](docs/CHAPTER_6_API.md)** — **BAB VI: API** (Endpoint Statistik Dinamis `/statistik`, Layanan Surat, & Pengaduan).
- 💻 **[CHAPTER_7_INSTALLATION_GUIDE.md](docs/CHAPTER_7_INSTALLATION_GUIDE.md)** — **BAB VII: Panduan Instalasi** (Development & Deployment Hostinger/cPanel).
- 📖 **[CHAPTER_8_USER_GUIDE.md](docs/CHAPTER_8_USER_GUIDE.md)** — **BAB VIII: Panduan Pengguna** (Operator Admin Panel 5 Tab Penduduk & 4 Tab Keluarga, serta Publik).
- 🧪 **[CHAPTER_9_TESTING.md](docs/CHAPTER_9_TESTING.md)** — **BAB IX: Pengujian** (PHPUnit / Pest Automated Testing 100% PASS & Matriks UAT).
- 🔧 **[CHAPTER_10_MAINTENANCE.md](docs/CHAPTER_10_MAINTENANCE.md)** — **BAB X: Pemeliharaan** (Storage Link Hosting, Hardening Keamanan, & Clear Cache).
- 📎 **[APPENDIX.md](docs/APPENDIX.md)** — **Lampiran A-D** (Summary Changelog, Screenshots, ERD, & Directory Tree).
- 📜 **[CHANGELOG.md](docs/CHANGELOG.md)** — Riwayat Perubahan Versi Lengkap (*Keep a Changelog*).

---

## 🚀 Fitur Utama

- **Dashboard Statistik Interaktif**: Grafik & diagram data kependudukan, pekerjaan, pendidikan, disabilitas, dan penyakit kronis secara real-time yang dihitung dari database warga mikro. Supports **Horizontal Stacked Bar Chart** untuk perbandingan 2 arah.
- **Transparansi APBDes**: Visualisasi realisasi anggaran pendapatan, belanja, dan pembiayaan desa lengkap dengan diagram donat alokasi dan progress bar pencapaian.
- **Portal Informasi & Publikasi Desa**:
  - **Berita & Kegiatan**: Publikasi artikel berita dengan fitur SEO meta lengkap & schema JSON-LD, serta kompresi otomatis pratinjau WhatsApp.
  - **Pengumuman Resmi**: Daftar pengumuman terintegrasi dengan fitur **Baca Cepat** berbasis accordion.
  - **Galeri Multi-tipe**: Galeri visual terintegrasi untuk foto kegiatan dan video tautan YouTube.
  - **Lembaga Desa**: Halaman profil lembaga kemasyarakatan desa (`/lembaga`) terstruktur dalam grid modern dengan logo opsional.
  - **Arsip Dokumen**: Portal unduhan keputusan kepala desa, peraturan desa, dan dokumen administrasi publik lainnya.
  - **Potensi Desa**: Galeri dan deskripsi sektor pariwisata, komoditas pertanian/perkebunan, peternakan, industri kreatif, seni & budaya.
- **Data Mikro SDGs & Regsosek**:
  - Model kependudukan mikro terintegrasi: `Dusun` $\rightarrow$ `Keluarga (Family)` $\rightarrow$ `Penduduk (Citizen)`.
  - Panel admin Filament untuk input kuesioner keluarga dan data penduduk.
- **Layanan Mandiri & Pengaduan Warga**: Layanan permohonan surat administrasi desa, pelacakan status permohonan via nomor tiket, formulir pengaduan warga online, serta buku tamu digital terintegrasi.
- **Peta Spasial Desa**: Pemetaan interaktif wilayah administratif dusun, batas wilayah, dan titik koordinat sebaran fasilitas umum berbasis Leaflet.js.

---

## 🛠️ Stack Teknologi

- **Framework**: [Laravel 12](https://laravel.com) (PHP 8.3+)
- **Admin Panel**: [Filament v4](https://filamentphp.com)
- **CSS Engine**: [Tailwind CSS v4](https://tailwindcss.com)
- **Interaktivitas**: Alpine.js, ApexCharts, Leaflet.js
- **Database**: MySQL / MariaDB / PostgreSQL / SQLite / SQL Server

---

## 💻 Ringkasan Cara Menjalankan Aplikasi

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/kalamangna/cms-desa.git
   cd cms-desa
   ```
2. **Install Dependensi & Konfigurasi**:
   ```bash
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   ```
3. **Migrasi & Seed Data**:
   ```bash
   php artisan migrate:fresh --seed
   ```
4. **Kompilasi Aset & Run Dev Server**:
   ```bash
   npm run dev
   php artisan serve
   ```
*Untuk panduan lengkap instalasi dan deployment server produksi, silakan baca [docs/CHAPTER_7_INSTALLATION_GUIDE.md](docs/CHAPTER_7_INSTALLATION_GUIDE.md).*
