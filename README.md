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

### 🛠️ Lingkungan Lokal (Development)

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

### 🌐 Deployment Server Produksi (Hostinger / cPanel)

1. **Clone Repositori & Install Dependensi**:
    ```bash
    git clone https://github.com/kalamangna/cms-desa.git
    cd cms-desa
    composer install --no-dev --optimize-autoloader
    cp .env.example .env
    php artisan key:generate
    ```
2. **Migrasi Database**:
    ```bash
    php artisan migrate:fresh --seed
    ```
    *(Catatan: Jika `php artisan storage:link` gagal karena fungsi `symlink()` dilarang oleh server shared hosting, hal ini aman untuk dilewati).*
3. **Hubungkan `public_html` ke Folder Public**:
    ```bash
    cd ..
    rm -rf public_html
    ln -s cms-desa/public public_html
    ```
4. **Optimasi Performa & Inisialisasi Media (`/init`)**:
    ```bash
    cd cms-desa
    php artisan config:cache && php artisan route:cache && php artisan view:cache
    ```
    *Setelah login ke Admin Panel Filament, buka URL `https://domain-desa.id/init` di browser. Fitur ini dibuat khusus untuk menyalin seluruh aset/media secara fisik dari storage ke folder publik tanpa membutuhkan fungsi symlink.*

---

### 🚀 Otomatisasi Deployment (Multi-Server)

Untuk mengelola instalasi di beberapa server sekaligus tanpa perlu SSH manual satu-per-satu, gunakan *script* `deploy.sh` yang telah disediakan.
1. Salin `deploy.sh` (jika belum ada) dan sesuaikan konfigurasi *username*, IP, dan *path* untuk masing-masing server.
2. Pastikan file `deploy.sh` sudah dimasukkan ke dalam `.gitignore` untuk mencegah kebocoran kredensial server.
3. Jalankan script:
    ```bash
    chmod +x deploy.sh
    ./deploy.sh
    ```

---

**Dikembangkan oleh [kalamangna](https://github.com/kalamangna)**
