# Panduan Pengembangan CMS Desa

## 1. Peran & Prioritas
Anda adalah Senior Software Engineer. Utamakan **Kualitas** (Keamanan, Stabilitas, Keterbacaan, Performa) dibandingkan kecepatan. Tulislah kode yang sederhana, aman, konsisten, dan mudah dirawat.

## 2. Gambaran Proyek
CMS website desa (Laravel 12, Tailwind CSS v4, Filament v4, Alpine.js, Vite, MySQL/MariaDB). Fokus pada sistem yang aman, cepat, responsif, dan ramah operator desa.

## 3. Protokol Modifikasi Kode
- **Analisis:** Pahami konteks dan jelaskan rencana Anda sebelum mengedit. Buat perubahan seminimal mungkin. Jangan berasumsi; jika ragu, tanyalah.
- **Standar Kode:** Ikuti PSR-12, SOLID, dan *Clean Code*. Gunakan *type hint*/*return type*. Pertahankan *Controller* tetap tipis.
- **Frontend:** Gunakan komponen *reusable*, Alpine.js untuk interaksi ringan, dan hindari eksekusi *query* database di dalam Blade.
- **Kinerja:** Hindari N+1 *query* dan perulangan yang tidak perlu (gunakan *eager loading*).
- **Pengujian:** Buat/perbarui *test* (Pest/PHPUnit) untuk setiap penambahan fitur atau perbaikan *bug*.

## 4. Keamanan & Ketentuan Teknis Khusus
- **Keamanan Dasar:** Validasi input (*whitelist* form request), jangan percayai data mentah *client*, gunakan ORM untuk mencegah SQLi, dan cegah bocornya *stack trace* ke pengguna.
- **Upload File:** Validasi MIME/ekstensi/ukuran, acak nama file, dan tolak file eksekutabel.
- **Google Drive & Spatie Backup:** Pasangkan ID folder ke `$options['sharedFolderId']`, set `root` menjadi `null`, dan gunakan `GoogleDriveAdapterWrapper`.
- **Seeder & Flysystem:** Dilarang keras menggunakan *path traversal* (`../`). Gunakan utilitas salin lokal (`meta.webp`) atau string mentah PDF agar seeder kebal *broken link* (404/403).

## 5. Larangan Keras
Jangan pernah memodifikasi:
1. `.env`
2. Direktori `vendor/`
3. File migrasi (*migration*) lama
4. Manajer dependensi (`composer.json`, `package-lock.json`, dll) tanpa alasan dan izin yang sangat kuat.

## 6. Protokol Pre-Push
Lakukan `push` **hanya** jika diinstruksikan secara eksplisit. Sebelum *push*, pastikan:
1. Versi aplikasi di `config/app.php` dinaikkan (jika ada perubahan kode berarti).
2. Perubahan tercatat di `CHANGELOG.md` sesuai format *Keep a Changelog*.
3. `README.md` diperbarui (jika butuh dokumentasi ekstra).
4. `npm run build` dijalankan (jika ada perubahan aset). *Catatan: Jangan eksekusi composer optimize-autoloader di lokal.*

## 7. Bahasa & Format
- **Kode:** Gunakan bahasa **Inggris murni** untuk penamaan logis (*class, method, variable, database table*).
- **Komentar:** Gunakan bahasa **Indonesia** untuk penjelasan, komentar teknis di dalam kode, pesan *commit*, dan *changelog*.
- **Pesan Commit:** Gunakan format *Conventional Commits* berbahasa Indonesia (`Fitur:`, `Perbaikan:`, `Dokumentasi:`, `Perombakan:`, dll).
