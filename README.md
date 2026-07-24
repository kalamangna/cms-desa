# 🏛️ Portal Resmi Website Desa

Portal Informasi Desa Modern, Transparan, dan Berbasis Data Mikro. Menyajikan visualisasi data sosial ekonomi (Regsosek/SDGs Desa) secara interaktif.

---

## 🚀 Fitur Utama

- **Data Mikro Kependudukan (Regsosek & SDGs)**: Pengelolaan hierarki wilayah `Dusun` $\rightarrow$ `Keluarga` $\rightarrow$ `Penduduk` melalui panel admin Filament terintegrasi.
- **Dashboard Statistik Dinamis**: Visualisasi grafik kependudukan secara real-time. Mendukung format perbandingan 1 arah dan 2 arah (_Horizontal Stacked Bar Chart_) serta fitur ekspor tabel ber-Kop Header resmi Pemerintah Desa (CSV, Excel, PDF).
- **Manajemen Konten (CMS)**:
    - **Berita & Kegiatan**: Artikel terpublikasi dengan kompresi otomatis gambar (menjamin pratinjau WhatsApp) dan struktur metadata SEO (`JSON-LD`).
    - **Pengumuman Resmi**: Daftar pengumuman interaktif bergaya _accordion_ (Baca Cepat).
    - **Galeri & Infografis**: Pengelolaan multi-tipe visual (Foto, embed YouTube) serta fitur **Infografis Popup** untuk pesan darurat/iklan layanan masyarakat di halaman depan.
- **Profil Pemerintahan Desa**: Manajemen daftar susunan **Aparatur Desa** dan tata kelola **Lembaga Kemasyarakatan**, lengkap dengan deskripsi fungsional serta bagan hierarki.
- **Peta Spasial & GIS Desa**: Pemetaan interaktif Leaflet.js untuk delineasi batas wilayah administratif dusun dan titik persebaran (_markers_) **Fasilitas Publik / Umum**.
- **Keterbukaan Informasi Publik**:
    - **Transparansi APBDes**: Visualisasi donat alokasi anggaran dan progress bar serapan biaya secara presisi.
    - **Repositori Unduhan**: Pengarsipan Dokumen Publik (SK, Perdes), Publikasi Desa (Laporan/Buku Terbitan), dan Dataset Terbuka (Open Data CSV/Excel) dengan mekanisme pencatatan riwayat unduhan.
    - **Potensi Desa**: Basis data kekayaan lokal meliputi sektor pariwisata, pertanian, peternakan, hingga industri kreatif.
- **Layanan Mandiri & Interaksi Warga**:
    - **Permohonan Surat**: Sistem _self-service_ pencetakan surat administrasi berbekal identifikasi NIK, disertai fitur penerbitan dan pelacakan **Nomor Tiket**.
    - **Pengaduan Online & Buku Tamu**: Mekanisme umpan balik (_feedback_) warga langsung ke antrean tinjauan dashboard operator desa.
- **Konfigurasi Global & Keamanan (Tanpa Coding)**:
    - Manajemen identitas logo desa, kontak, tautan medsos, metadata SEO, serta **Pemilih Tema Warna Dinamis** dari pengaturan admin.
    - Skema autentikasi kokoh berbasis _Role-Based Access Control_ (RBAC) dengan Spatie Permissions.

---

## 🛠️ Stack Teknologi

- **Framework**: [Laravel 12](https://laravel.com) (PHP 8.3+)
- **Admin Panel**: [Filament v4](https://filamentphp.com)
- **CSS Engine**: [Tailwind CSS v4](https://tailwindcss.com)
- **Interaktivitas**: Alpine.js, ApexCharts, Leaflet.js
- **Database**: MySQL / MariaDB / PostgreSQL / SQLite / SQL Server

---

## 💻 Panduan Instalasi

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

---

Dikembangkan oleh [kalamangna](https://github.com/kalamangna)
