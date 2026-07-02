# 🏛️ Portal Resmi Desa Cantik (Cinta Statistik)

Portal Informasi Desa Modern, Transparan, dan Berbasis Data Mikro. Dibangun khusus untuk memenuhi standar penilaian **Program Desa Cantik BPS** serta menyajikan visualisasi data sosial ekonomi (Regsosek/SDGs Desa) secara interaktif.

---

## 🚀 Fitur Utama

- **Dashboard Statistik Interaktif**: Grafik & diagram data kependudukan, pekerjaan, pendidikan, disabilitas, dan penyakit kronis secara real-time yang dihitung dari database warga mikro.
- **Transparansi APBDes**: Visualisasi realisasi anggaran pendapatan, belanja, dan pembiayaan desa lengkap dengan diagram donat alokasi dan progress bar pencapaian.
- **Portal Informasi Desa**: Modul publikasi berita kegiatan, pengumuman resmi desa (dilengkapi accordion), galeri foto & video, serta dokumen arsip keputusan/peraturan desa.
- **Data Mikro SDGs & Regsosek**:
  - Model kependudukan mikro terintegrasi: `Dusun` -> `Keluarga (Family)` -> `Penduduk (Citizen)`.
  - Panel admin Filament untuk input kuesioner keluarga (karakteristik bangunan, sanitasi, listrik, kepemilikan aset) dan data penduduk (BPJS, PIP, disabilitas, riwayat penyakit).
- **Layanan Mandiri Warga**: Katalog panduan administrasi pengurusan surat/layanan desa dengan persyaratan yang dapat di-expand (*collapsible*).
- **Desain Glassmorphism Premium**: Tampilan frontend modern dengan transisi halus, navigasi dinamis (blur saat scroll), layout grid modular, serta responsive total.

---

## 🛠️ Stack Teknologi

- **Framework**: [Laravel 12](https://laravel.com)
- **Admin Panel**: [Filament v4](https://filamentphp.com)
- **CSS Engine**: [Tailwind CSS v4](https://tailwindcss.com)
- **Interaktivitas**: Alpine.js, Chart.js, Leaflet.js
- **Database**: MySQL / PostgreSQL / SQLite

---

## 💻 Panduan Instalasi Lokal

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/kalamangna/cms-desa.git
   cd cms-desa
   ```

2. **Install Dependensi**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Sesuaikan konfigurasi database Anda di file `.env`.*

4. **Migrasi & Seed Data Master**:
   ```bash
   php artisan migrate --seed
   ```

5. **Kompilasi Aset Frontend**:
   ```bash
   npm run dev # Menjalankan dev server, atau
   npm run build # Kompilasi build produksi
   ```

---

## 🌐 Panduan Deployment di Server Hostinger (hPanel)

Karena keterbatasan server shared hosting Hostinger (hPanel) yang menonaktifkan fungsi terminal `exec()` dan fungsi PHP native `symlink()`, sistem ini telah dikonfigurasi untuk menyimpan data upload secara langsung ke folder publik tanpa membutuhkan symbolic link.

### 1. Cloning Repositori di SSH
Login ke SSH Hostinger Anda, masuk ke direktori satu tingkat di atas `public_html`, lalu jalankan:
```bash
git clone git@github.com:kalamangna/cms-desa.git project-desa
```

### 2. Konfigurasi Awal di Server
1. Pindah ke folder proyek: `cd project-desa`
2. Install dependensi: `composer install --optimize-autoloader --no-dev`
3. Konfigurasi file `.env` (sesuaikan database hPanel Anda).
4. Buat tautan folder `public_html` ke folder `public` proyek:
   ```bash
   rm -rf ~/public_html
   ln -s ~/project-desa/public ~/public_html
   ```

### 3. Migrasi & Setup File Storage
Jalankan perintah berikut di terminal SSH:
```bash
php artisan migrate --force --seed
php artisan optimize:clear
```

Buka browser Anda dan jalankan rute inisialisasi ini untuk menyalin file media bawaan seeder secara fisik:
👉 **`https://tompobulu.desa.id/init-link`**

*(Jika berhasil, folder fisik `public/storage` akan terbuat secara otomatis dan seluruh file media bawaan akan disalin ke dalamnya).*

---

## 🔄 Alur Update Website (Workflow Harian)

Setiap kali Anda melakukan pembaruan kode di laptop lokal, ikuti alur berikut untuk menerapkannya ke server produksi:

**Di Laptop Lokal (Lokal):**
1. Selesaikan penulisan kode/perbaikan.
2. Jalankan build produksi CSS/JS:
   ```bash
   npm run build
   ```
3. Commit dan push ke GitHub:
   ```bash
   git add .
   git commit -m "Deskripsi perubahan Anda"
   git push origin main
   ```

**Di Server Hostinger (SSH):**
1. Tarik perubahan terbaru dari repositori:
   ```bash
   cd ~/project-desa
   git pull origin main
   ```
2. Jalankan migrasi (jika ada perubahan struktur tabel):
   ```bash
   php artisan migrate --force
   ```
3. Bersihkan cache rute, config, dan views Laravel:
   ```bash
   php artisan optimize:clear
   ```

---

## 📝 Catatan Penting & Troubleshooting

- **Error 403 / Gagal Upload Gambar**: Hal ini terjadi jika folder `public/storage` belum terbuat secara fisik di server atau tidak memiliki izin menulis. Pastikan Anda telah mengakses rute `https://tompobulu.desa.id/init-link` setelah melakukan deployment awal.
- **Hak Akses Folder**: Jika terjadi error izin tulis, set hak akses folder dengan perintah:
   ```bash
   chmod -R 755 public/storage storage bootstrap/cache
   ```
- **Fallback Gambar Kosong**: Jika data kependudukan atau galeri tidak memiliki file gambar, sistem otomatis memuat berkas lokal `/img/meta.png` sebagai gambar fallback yang aman dan responsif.
- **Versi PHP**: Pastikan akun hosting Hostinger Anda diset menggunakan **PHP 8.2** atau yang lebih baru.

---
*Dibuat oleh Tim Pengembang Website Desa Cantik.*
