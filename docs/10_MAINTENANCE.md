# BAB X: PEMELIHARAAN (CHAPTER_10_MAINTENANCE.md)

---

## 10.1 Solusi Kendala Hosting & Storage Link
Jika gambar media tidak muncul di server Hostinger (akibat `symlink()` ditutup), jalankan pembuat symlink via terminal SSH:
```bash
rm -rf ~/public_html
ln -s ~/cms-desa/public ~/public_html
```

---

## 10.2 Hardening Keamanan & Optimasi Cache
Jalankan pembersihan cache rutin:
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
