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
    - Skema autentikasi kokoh berbasis _Role-Based Access Control_ (RBAC) dengan pemisahan hak akses tegas antara `super_admin` (Pengembang) dan `admin_desa` (Operator).
    - **Sistem Cadangan (Backup)**: Pencadangan penuh (basis data dan berkas) yang terpusat di panel admin, beroperasi harian secara otomatis di latar belakang dengan dukungan penyimpanan ganda (Server Lokal & Remote Google Drive).

---

## 🛠️ Stack Teknologi

- **Framework**: [Laravel 12](https://laravel.com) (PHP 8.2+)
- **Admin Panel**: [Filament v4](https://filamentphp.com)
- **CSS Engine**: [Tailwind CSS v4](https://tailwindcss.com)
- **Interaktivitas**: Alpine.js, ApexCharts, Leaflet.js
- **Cloud Storage**: Flysystem Google Drive (`masbug/flysystem-google-drive-ext`)
- **Database**: MySQL / MariaDB / PostgreSQL / SQLite / SQL Server

---

## 📖 Dokumentasi Developer

- **[GEMINI.md](GEMINI.md)** — Arsitektur aplikasi, standar & konvensi pengembangan
- **[DESIGN.md](DESIGN.md)** — Sistem desain & panduan UI (Tailwind CSS)
- **[CRON.md](CRON.md)** — Setup cron job per-desa (shared hosting)

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

### 🔧 Alur Kerja Development

Perintah utama development (menjalankan server, queue, log, dan vite sekaligus):
```bash
composer dev
```

Menjalankan pengujian:
```bash
composer test                                     # Seluruh suite
php artisan test --filter=StatisticDashboardTest  # Satu test class
```

Format kode:
```bash
./vendor/bin/pint
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

### ☁️ Konfigurasi Remote Backup (Google Drive - Opsional)

Untuk mengaktifkan pencadangan otomatis ke Google Drive di luar server lokal:

1. **Buat Google Cloud Project**:
   - Buka [Google Cloud Console](https://console.cloud.google.com)
   - Buat project baru atau pilih project yang ada
   - Aktifkan **Google Drive API** di menu APIs & Services

2. **Buat OAuth 2.0 Credentials**:
   - Di Google Cloud Console, buka **APIs & Services → Credentials**
   - Klik **Create Credentials → OAuth client ID**
   - Pilih tipe aplikasi **Desktop app** atau **Web application**
   - Salin **Client ID** dan **Client Secret**

3. **Generate Refresh Token**:
   - Gunakan [Google OAuth 2.0 Playground](https://developers.google.com/oauthplayground)
   - Atau tools seperti [oauth2l](https://github.com/google/oauth2l)
   - Authorize dengan scope `https://www.googleapis.com/auth/drive.file`
   - Salin **Refresh Token**

4. **Buat Folder di Google Drive**:
   - Buka Google Drive, buat folder baru untuk backup
   - Copy ID folder dari URL (format: `https://drive.google.com/drive/folders/FOLDER_ID_DI_SINI`)

5. **Isi kredensial ke `.env`**:

```env
GOOGLE_DRIVE_CLIENT_ID=XXXXX.apps.googleusercontent.com
GOOGLE_DRIVE_CLIENT_SECRET=GOCSPX-XXXXX
GOOGLE_DRIVE_REFRESH_TOKEN=1//04XXXXX
GOOGLE_DRIVE_FOLDER_ID=1JRLzo8AP0B8ZeuJal2CEM2PRGJq83ZCc
```

*Sistem secara otomatis akan mendeteksi kredensial tersebut dan menyimpan berkas cadangan harian di server lokal DAN Google Drive Desa.*

---

**Dikembangkan oleh [kalamangna](https://github.com/kalamangna)**
