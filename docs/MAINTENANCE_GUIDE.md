# MAINTENANCE GUIDE DOCUMENT (MAINTENANCE_GUIDE.md)
## BAB X: PEMELIHARAAN SISTEM

---

## BAB X: PEMELIHARAAN

### 10.1 Solusi Kendala Hosting & Storage Link
Jika gambar media tidak muncul di server Hostinger (akibat fungsi `symlink()` ditutup), jalankan pembuat symlink via terminal SSH:
```bash
rm -rf ~/public_html
ln -s ~/cms-desa/public ~/public_html
```

### 10.2 Hardening Keamanan & Pembersihan Cache Rutin
Jalankan perintah pembersihan cache Laravel secara berkala:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Rekonstruksi Cache Produksi
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
