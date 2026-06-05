# Website Desa Cantik (Cinta Statistik)

Portal Informasi Desa Modern, Transparan, dan Berbasis Data. Dibangun khusus untuk memenuhi standar penilaian **Program Desa Cantik BPS**.

## 🚀 Fitur Utama

- **Dashboard Statistik Interaktif**: Visualisasi data kependudukan dan sosial secara real-time.
- **Transparansi APBDes**: Laporan realisasi anggaran dengan grafik alokasi dana yang informatif.
- **Portal Berita & Warta**: Sistem informasi kegiatan desa dengan layout profesional.
- **Digital Archive (Open Data & Publikasi)**: Katalog dataset (CSV/XLS) dan dokumen profil desa (PDF) yang siap unduh.
- **Peta Desa Interaktif**: Integrasi Leaflet JS untuk pemetaan fasilitas dan potensi wilayah.
- **Backend Admin (Filament)**: Panel administrasi yang mudah digunakan untuk mengelola seluruh konten.
- **Mobile First Design**: Tampilan yang dioptimalkan untuk perangkat mobile dan desktop.

## 🛠️ Stack Teknologi

- **Framework**: [Laravel 11](https://laravel.com)
- **Admin Panel**: [Filament v4](https://filamentphp.com)
- **CSS Framework**: [Tailwind CSS 4](https://tailwindcss.com)
- **Icons**: [FontAwesome 6](https://fontawesome.com)
- **Interactivity**: Alpine.js, Chart.js, Leaflet.js
- **Database**: MySQL / SQLite

## 💻 Instalasi Lokal

1. **Clone Proyek**:
   ```bash
   git clone <url-repository>
   cd desa-cantik
   ```

2. **Install Dependensi**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Lingkungan**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi & Seed Data**:
   ```bash
   php artisan migrate --seed
   ```

5. **Symlink Storage**:
   ```bash
   php artisan storage:link
   ```

6. **Build Aset**:
   ```bash
   npm run dev # atau npm run build untuk produksi
   ```

## 🌐 Panduan Deployment (hPanel Hostinger)

1. **Upload File**: Unggah seluruh file (kecuali `vendor` dan `node_modules`) ke root direktori hosting Anda.
2. **Database**: Buat database MySQL di hPanel dan update file `.env` di server.
3. **Instalasi via SSH**:
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan migrate --force --seed
   php artisan storage:link
   ```
4. **Optimasi Produksi**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## 📝 Catatan Tambahan

- Pastikan versi PHP pada hosting minimal **8.2**.
- Aktifkan **SSL (HTTPS)** untuk keamanan data dan akses peta yang optimal.
- Seluruh ikon menggunakan **FontAwesome 6** via CDN yang terkonfigurasi di `layouts/app.blade.php`.

---
*Dibuat oleh Tim Pengembang Desa Cantik.*
