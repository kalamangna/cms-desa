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

## 🌐 Panduan Deployment (Git-Based di hPanel Hostinger)

Menggunakan Git di server memudahkan Anda untuk melakukan pembaruan kode hanya dengan perintah `git pull`.

### 1. Persiapan SSH di hPanel
1. Masuk ke hPanel Hostinger > **Advanced** > **SSH Access**.
2. Aktifkan (Enable) SSH Access.
3. Catat **SSH IP**, **Port**, **Username**, dan **Password**.

### 2. Konfigurasi SSH Key (Untuk Akses Aman)
Agar server hPanel dapat mengakses repositori GitHub Anda secara otomatis:
1. Login ke SSH via Terminal: `ssh -p <port> <username>@<ip>`
2. Generate SSH Key:
   ```bash
   ssh-keygen -t ed25519 -C "email@anda.com"
   ```
3. Tekan Enter terus (kosongkan passphrase).
4. Ambil isi public key: `cat ~/.ssh/id_ed25519.pub`
5. Salin teks tersebut dan masukkan ke akun GitHub Anda (**Settings** > **SSH and GPG keys** > **New SSH Key**).

### 3. Cloning Repositori
Masuk ke root direktori hPanel (satu tingkat di atas `public_html`) dan jalankan:
```bash
git clone git@github.com:kalamangna/cms-desa.git project-desa
```

### 4. Setup Laravel & Symlink
1. Pindah ke folder proyek: `cd project-desa`
2. Install dependensi: `composer install --optimize-autoloader --no-dev`
3. Konfigurasi `.env`: `cp .env.example .env` (sesuaikan data database hPanel).
4. Setup Public HTML:
   - Hapus folder `public_html` bawaan: `rm -rf ~/public_html`
   - Buat link dari `public` ke `public_html`:
     ```bash
     ln -s ~/project-desa/public ~/public_html
     ```
5. Jalankan perintah final:
   ```bash
   php artisan key:generate
   php artisan migrate --force --seed
   php artisan storage:link
   ```

### 5. Cara Update Website di Masa Depan
Kapanpun Anda melakukan push dari komputer lokal ke GitHub, cukup jalankan ini di server:
```bash
cd ~/project-desa
git pull origin main
php artisan migrate --force
php artisan config:cache
```

## 📝 Catatan Tambahan

- Pastikan versi PHP pada hosting minimal **8.2**.
- Aktifkan **SSL (HTTPS)** untuk keamanan data dan akses peta yang optimal.
- Seluruh ikon menggunakan **FontAwesome 6** via CDN yang terkonfigurasi di `layouts/app.blade.php`.

---
*Dibuat oleh Tim Pengembang Desa Cantik.*
