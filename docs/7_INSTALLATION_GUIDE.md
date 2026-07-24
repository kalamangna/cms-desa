# BAB VII: PANDUAN INSTALASI (7_INSTALLATION_GUIDE.md)

---

## 7.1 Pemasangan Lingkungan Lokal (Development)
```bash
git clone https://github.com/kalamangna/cms-desa.git
cd cms-desa
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run dev
php artisan serve
```

---

## 7.2 Deployment Server Produksi (Hostinger/cPanel)
```bash
git clone https://github.com/kalamangna/cms-desa.git
cd cms-desa
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
rm -rf ~/public_html
ln -s ~/cms-desa/public ~/public_html
php artisan config:cache && php artisan route:cache && php artisan view:cache
```
