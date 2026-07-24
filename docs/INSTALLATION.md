# PANDUAN INSTALASI & DEPLOYMENT (INSTALLATION.md)

Dokumen ini berisi instruksi lengkap untuk pemasangan aplikasi **Portal Resmi Website & CMS Desa** baik di lingkungan lokal (*development*) maupun di server produksi (*shared hosting/VPS*).

---

## 💻 1. Persyaratan Sistem (System Requirements)

- **PHP**: versi >= 8.3 dengan ekstensi: `BCMath`, `Ctype`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `Tokenizer`, `XML`, `GD/Imagick`.
- **Database Engine**: MySQL 8.0+ / MariaDB 10.5+ / PostgreSQL / SQLite.
- **Package Manager**:
  - [Composer](https://getcomposer.org/) v2.x
  - [Node.js](https://nodejs.org/) v18+ & npm
- **Web Server**: Nginx / Apache (dengan modul `mod_rewrite` aktif).

---

## 🛠️ 2. Panduan Instalasi Lokal (Development Environment)

### Langkah 1: Clone Repositori
```bash
git clone https://github.com/kalamangna/cms-desa.git
cd cms-desa
```

### Langkah 2: Install Dependensi PHP & Node.js
```bash
composer install
npm install
```

### Langkah 3: Konfigurasi File Berkas `.env`
```bash
cp .env.example .env
php artisan key:generate
```
Edit file `.env` dan atur kredensial koneksi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=desa_cms
DB_USERNAME=root
DB_PASSWORD=
```

### Langkah 4: Migrasi & Inisialisasi Data
```bash
php artisan migrate:fresh --seed
```

### Langkah 5: Kompilasi Aset Frontend & Jalankan Server Lokal
```bash
# Jalankan Vite dev server
npm run dev

# Di terminal terpisah, jalankan Laravel dev server
php artisan serve
```
Buka browser Anda di `http://127.0.0.1:8000`.

---

## 🌐 3. Panduan Deployment Produksi (Hostinger / cPanel / Shared Hosting)

Pada shared hosting (seperti Hostinger hPanel atau cPanel), tautan direktori `public` perlu disesuaikan dengan folder publik server (`public_html`).

### Langkah 1: Cloning via Terminal SSH
Masuk ke terminal SSH server Anda, berpindah ke direktori satu tingkat di atas `public_html`:
```bash
git clone https://github.com/kalamangna/cms-desa.git
cd cms-desa
```

### Langkah 2: Install Dependensi Tanpa Dev Packages
```bash
composer install --no-dev --optimize-autoloader
```

### Langkah 3: Konfigurasi Production `.env`
```bash
cp .env.example .env
php artisan key:generate
```
Sesuaikan environment untuk produksi:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://desa-anda.go.id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=u123456_desa
DB_USERNAME=u123456_admin
DB_PASSWORD=PasswordKuat123!
```

### Langkah 4: Eksekusi Migrasi Database
```bash
php artisan migrate --force
```

### Langkah 5: Linking Folder Public ke `public_html`
```bash
# Hapus folder public_html bawaan jika kosong
rm -rf ~/public_html

# Buat symbolic link dari public proyek ke public_html
ln -s ~/cms-desa/public ~/public_html
```

### Langkah 6: Optimization & Caching
Jalankan perintah pengompresan cache Laravel:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔑 4. Akun Default Operator Desa (Seed Data)

Setelah melakukan seeder (`php artisan db:seed`), akun admin default adalah:
- **URL Admin**: `https://domain-desa.id/admin`
- **Email**: `admin@desa.go.id`
- **Password**: `password` *(Harap segera ubah password ini di Filament Admin setelah login pertama)*.
