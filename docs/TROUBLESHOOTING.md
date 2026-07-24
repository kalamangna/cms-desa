# PANDUAN PENANGANAN MASALAH & FAQ (TROUBLESHOOTING.md)

Dokumen ini berisi daftar kendala teknis yang sering ditemui beserta langkah-langkah solusinya pada **Portal Resmi Website & CMS Desa**.

---

## 🛑 1. Kendala Deployment & Server Hosting

### A. Simbolik Link Storage Tidak Berfungsi (Error `symlink()` Disabled)
- **Gejala**: Gambar berita, sampul publikasi, atau foto aparatur tidak muncul di server hosting (seperti Hostinger hPanel).
- **Penyebab**: Server shared hosting menonaktifkan fungsi PHP `symlink()`.
- **Solusi**:
  Gunakan perintah pembuat symbolic link via terminal SSH:
  ```bash
  rm -rf ~/public_html
  ln -s ~/cms-desa/public ~/public_html
  ```
  Atau gunakan fitur salin berkas media fisik yang disediakan aplikasi di menu pengaturan.

---

### B. Gambar Pratinjau Tautan (WhatsApp Preview) Tidak Muncul
- **Gejala**: Saat tautan berita dibagikan ke WhatsApp, gambar pratinjau tidak muncul (hanya teks).
- **Penyebab**:
  1. Ukuran file gambar melebihi 300 KB (WhatsApp menolak file besar).
  2. Protocol URL gambar masih `http://` bukan `https://`.
- **Solusi**:
  - Aplikasi telah mengintegrasikan kompresi otomatis client-side saat mengunggah foto berita di Filament Admin (mengubah dimensi ke 1200x630px dengan ukuran file < 300 KB).
  - Pastikan variabel `APP_URL` di file `.env` menggunakan `https://`:
    ```env
    APP_URL=https://desa-anda.go.id
    ```

---

## 📊 2. Kendala Impor Data Kependudukan (Excel)

### A. Galeri `File tidak ditemukan` atau Memory Limit Exceeded
- **Gejala**: Proses impor Excel berhenti di tengah jalan atau muncul error memori PHP.
- **Penyebab**: Ukuran berkas Excel sangat besar atau nilai `memory_limit` PHP server terlalu rendah.
- **Solusi**:
  1. Naikkan `memory_limit` PHP di `.env` atau `php.ini` ke `512M` atau `1G`.
  2. Pastikan header kolom Excel kuesioner tidak diubah struktur nama kolomnya.

---

### B. NIK / No. KK Duplikat atau Data Tidak Terbaca
- **Gejala**: Data warga/keluarga di Excel tidak masuk ke database.
- **Solusi**:
  1. Periksa kolom NIK dan No. KK pada file Excel. NIK harus terdiri dari 16 digit angka valid.
  2. Format sel NIK di Excel sebaiknya diatur ke tipe `Text` (bukan `Number` / `Scientific`) untuk mencegah pemotongan digit 0 di depan.

---

## ⚡ 3. Pembersihan Cache & Pemeliharaan Rutin

Jika tampilan web tidak berubah setelah melakukan update kode atau pengubahan pengaturan di Filament Admin, jalankan perintah pembersihan cache Laravel via terminal SSH:

```bash
# 1. Bersihkan Seluruh Cache Aplikasi
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 2. Rekonstruksi Cache Produksi (Optimasi Performa)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
