# 🏛️ Portal Resmi Website Desa

Portal Informasi Desa Modern, Transparan, dan Berbasis Data Mikro. Menyajikan visualisasi data sosial ekonomi (Regsosek/SDGs Desa) secara interaktif.

---

## 📚 Dokumentasi Proyek (`docs/`)

Dokumentasi resmi sistem informasi ini disusun secara terstruktur di folder `docs/` mengikuti standar Rekayasa Perangkat Lunak 10 BAB (Bahasa Inggris pada nama berkas):

- 📑 **[TECHNICAL_REPORT.md](docs/TECHNICAL_REPORT.md)** — **Dokumen Laporan Teknis Utama** yang memuat Cover, Lembar Pengesahan, Kata Pengantar, Daftar Isi, BAB I s/d BAB X, dan Lampiran (A-D).
- 📋 **[SYSTEM_REQUIREMENTS.md](docs/SYSTEM_REQUIREMENTS.md)** — **BAB I & BAB II**: Pendahuluan, Maksud & Tujuan, Analisis Kebutuhan Fungsional (FR) & Non-Fungsional (NFR).
- 📐 **[SYSTEM_DESIGN.md](docs/SYSTEM_DESIGN.md)** — **BAB III & BAB IV**: Perancangan Arsitektur MVC, Data Flow Diagram, Implementasi Stacked Bar 2 Arah & Sticky Column.
- 🗄️ **[DATABASE.md](docs/DATABASE.md)** — **BAB V & Lampiran C**: Perancangan Basis Data, Relasi Entitas (ERD), dan Spesifikasi Tabel Utama.
- 🌐 **[API.md](docs/API.md)** — **BAB VI**: Dokumentasi API Endpoint Statistik Publik (`/statistik`), Layanan Mandiri, & Pengaduan Warga.
- 💻 **[INSTALLATION_GUIDE.md](docs/INSTALLATION_GUIDE.md)** — **BAB VII**: Panduan Pemasangan Lingkungan Lokal (Development) & Deployment Server Produksi (Hostinger/cPanel).
- 📖 **[USER_GUIDE.md](docs/USER_GUIDE.md)** — **BAB VIII**: Panduan Penggunaan untuk Operator Desa (Admin Panel Filament 5 Tab Penduduk & 4 Tab Keluarga) dan Publik.
- 🧪 **[TESTING_REPORT.md](docs/TESTING_REPORT.md)** — **BAB IX**: Hasil Pengujian Otomatis (*Automated Test* 100% PASS) & Matriks UAT (*User Acceptance Testing*).
- 🛠️ **[MAINTENANCE_GUIDE.md](docs/MAINTENANCE_GUIDE.md)** — **BAB X**: Pemeliharaan Sistem, Hardening Keamanan, Handling Symbolic Link Hosting, & Optimasi Cache.
- 📜 **[CHANGELOG.md](docs/CHANGELOG.md)** — **Lampiran A**: Riwayat Catatan Perubahan Versi (*Keep a Changelog*).

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
*Untuk panduan lengkap instalasi dan deployment server produksi, silakan baca [docs/INSTALLATION_GUIDE.md](docs/INSTALLATION_GUIDE.md).*
