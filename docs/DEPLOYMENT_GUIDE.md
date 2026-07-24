# PANDUAN DEPLOYMENT & TROUBLESHOOTING (DEPLOYMENT_GUIDE.md)

Dokumen ini berisi instruksi lengkap untuk pemasangan aplikasi, deployment di server produksi, serta solusi pemecahan masalah (*troubleshooting*) pada **Portal Resmi Website & CMS Desa**.

---

## 💻 BAB I: PANDUAN INSTALASI LOKAL (DEVELOPMENT)

### 1.1 Persyaratan Sistem (System Requirements)
- **PHP**: versi >= 8.3 dengan ekstensi: `BCMath`, `Ctype`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `Tokenizer`, `XML`, `GD/Imagick`.
- **Database Engine**: MySQL 8.0+ / MariaDB 10.5+ / PostgreSQL / SQLite.
- **Package Manager**: Composer v2.x & Node.js v18+ (npm).

### 1.2 Langkah Instalasi
```bash
# 1. Clone Repositori
git clone https://github.com/kalamangna/cms-desa.git
cd cms-desa

# 2. Install Dependensi PHP & Node.js
composer install
npm install

# 3. Konfigurasi Environment & Generate Key
cp .env.example .env
php artisan key:generate

# 4. Migrasi & Seed Data Inicial
php artisan migrate:fresh --seed

# 5. Kompilasi Aset Frontend & Dev Server
npm run dev
php artisan serve
```

---

## 🌐 BAB II: PANDUAN DEPLOYMENT PRODUKSI (HOSTINGER / cPANEL)

### 2.1 Cloning & Konfigurasi via SSH Server
```bash
# 1. Clone di direktori satu tingkat di atas public_html
git clone https://github.com/kalamangna/cms-desa.git
cd cms-desa

# 2. Install Dependensi Produksi
composer install --no-dev --optimize-autoloader

# 3. Setup .env Produksi & Generate Key
cp .env.example .env
php artisan key:generate
# (Sesuaikan kredensial DB dan APP_URL=https://domain-desa.id)

# 4. Eksekusi Migrasi Database
php artisan migrate --force
```

### 2.2 Linking Folder `public` ke `public_html`
```bash
rm -rf ~/public_html
ln -s ~/cms-desa/public ~/public_html
```

### 2.3 Optimasi Performance & Caching Produksi
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🛠️ BAB III: PANDUAN PENANGANAN MASALAH (TROUBLESHOOTING & FAQ)

### 3.1 Kendala Media/Gambar Tidak Tampil (`symlink()` Disabled)
- **Penyebab**: Server shared hosting (Hostinger hPanel) menonaktifkan fungsi PHP `symlink()`.
- **Solusi**: Pastikan tautan symbolic link dibuat menggunakan SSH `ln -s ~/cms-desa/public ~/public_html`.

### 3.2 Gambar Pratinjau WhatsApp (WhatsApp Link Preview) Tidak Muncul
- **Penyebab**: Ukuran file gambar > 300 KB atau protokol URL masih HTTP.
- **Solusi**: Unggah foto berita melalui Filament Admin. Sistem akan mengompresi gambar otomatis menjadi < 300 KB (1200x630px) dan memaksa protokol HTTPS.

### 3.3 Kendala Impor Excel (Memory Limit / Header Mismatch)
- **Solusi**:
  - Naikkan `memory_limit` PHP di `php.ini` ke `512M` atau `1G`.
  - Pastikan sel NIK/No. KK pada Excel di-set sebagai `Text` (bukan Number/Scientific) untuk mencegah digit nol depan terpotong.

### 3.4 Perintah Pembersihan Cache Rutin
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```
