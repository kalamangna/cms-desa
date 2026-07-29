# 🏛️ Website Desa — Instruksi Pengembangan & Catatan Kerja (Gemini Agent)

## 📌 Aturan Eksekusi Perintah "PUSH" (Pre-Push Checklist)
*   **Pembaruan Versi Sistem:** Setiap kali Anda (Gemini Agent) melakukan perubahan kode pada sistem, **pastikan untuk selalu menaikkan versi sistem** (`version`) pada file `config/app.php` (misal dari 1.8.5 ke 1.8.6) agar perubahannya selalu tercermin di footer website.
*   **Pencatatan Perubahan:** Setiap sebelum melakukan *push*, pastikan untuk mencatat semua perubahan yang telah dilakukan di dalam berkas `CHANGELOG.md` sesuai dengan format Keep a Changelog.
*   **Pembaruan Dokumentasi:** Perbarui berkas [README.md](file:///Users/abedzul/Desktop/htdocs/desa-cms/README.md) jika terdapat perubahan atau penambahan fitur baru yang memerlukan instruksi/konfigurasi tambahan.
*   **Kompilasi CSS (Tailwind 4 & Vite):** Wajib menjalankan perintah `npm run build` **sebelum eksekusi push** apabila terdapat perubahan pada berkas CSS (`.css`) atau aset frontend, meskipun pengguna sedang menjalankan dev server/watch. Jika perubahan hanya pada kode PHP, Config, Service, atau Dokumentasi, `npm run build` tidak perlu dijalankan.
*   **Dependensi Composer (Laravel 12 & Filament v4):** Perintah `composer install --no-dev --optimize-autoloader` **hanya dijalankan di server production** (misal via script deployment `deploy.sh`), bukan di lingkungan lokal sebelum *push*.
*   **Waktu Eksekusi Aturan & Larangan Push Otomatis:** Seluruh aturan di atas (kompilasi CSS jika diperlukan, pencatatan di CHANGELOG, pembaruan versi, git commit, dan git push) **HANYA dijalankan jika ada instruksi "push" eksplisit dari pengguna**. Agen **DILARANG HARAM** melakukan `git push` secara otomatis, menawarkan konfirmasi push berulang kali tanpa diminta, atau berasumsi melakukan push setelah menyelesaikan perbaikan kode.

---

## 🛠️ Project Overview & Tech Stack
- **Deskripsi**: CMS Website Desa berbasis Laravel 12 & Tailwind CSS v4.
- **Tujuan**: Mudah dipelihara, aman, cepat, responsif, dan ramah operator desa.
- **Tech Stack**: Laravel 12, PHP 8.3+, Filament v4, Tailwind CSS v4, Alpine.js, Vite, MySQL / MariaDB.

---

## 📐 Standards & Rules Pengkodean (Coding Standards)

### General Rules
- Selalu pahami konteks dan analisis terlebih dahulu sebelum mengubah kode.
- Jelaskan rencana perubahan dan lakukan perubahan seminimal mungkin.
- Pertahankan struktur proyek yang ada. Jangan membuat perubahan besar tanpa persetujuan.

### Standards & Best Practices
- Ikuti PSR-12, Laravel Best Practices, SOLID Principles, dan Clean Code.
- Gunakan Type Hint, Return Type, dan Constructor Property Promotion jika memungkinkan.
- Hindari function terlalu panjang, nested if berlebihan, dan duplicate code.

### Architecture & Logic
- **Controller**: Controller harus tetap tipis. Business logic berada di `Service`, `Action`, atau `Helper` (jika diperlukan).
- **Validation**: Selalu gunakan `Form Request` untuk validasi data.
- **Database**: Gunakan Migration, Seeder, Factory, dan Eloquent Relationship. Hindari Query Builder di View dan Raw Query jika tidak diperlukan. Optimalkan eager loading, indexing, dan pagination.
- **Blade**: Gunakan Blade Components & Layout. Hindari query database dan logika kompleks di Blade.
- **Tailwind CSS & JS**: Prioritaskan reusable component & responsive design. Gunakan Alpine.js untuk interaktivitas ringan.

### Security & Performance
- **Security**: Periksa Authorization, Authentication, CSRF, XSS, Mass Assignment, File Upload, dan Validation. Jangan pernah menghapus middleware keamanan, menonaktifkan CSRF, atau menyimpan password tanpa hashing.
- **Performance**: Prioritaskan eager loading, caching, pagination, dan lazy loading asset. Hindari N+1 Query.
- **Google Drive Storage Adapter**: Saat mengonfigurasi `masbug/flysystem-google-drive-ext` pada Laravel Filesystem / Spatie Backup, ID folder target harus dipasangkan ke `$options['sharedFolderId']` dan parameter root diisi `null` agar berkas disimpan presisi di dalam folder target. Gunakan `GoogleDriveAdapterWrapper` untuk menangkap `UnableToReadFile` pada siklus pembuatan folder & pengecekan ketersediaan Spatie Backup.

---

## 📝 Workflow Komunikasi (Before & After Editing)

### Before Editing
Sebelum mengubah kode:
1. Jelaskan masalah.
2. Jelaskan solusi.
3. Sebutkan file yang akan diubah.
4. Jelaskan dampaknya.

### After Editing
Setelah selesai:
Berikan ringkasan:
1. File yang diubah.
2. Alasan perubahan.
3. Dampak perubahan.
4. Potensi risiko & cara pengujian.

---

## 🚫 Larangan Utuh (Forbidden)
Jangan pernah:
- Mengubah file `.env`.
- Mengubah folder `vendor/`.
- Menghapus migration lama.
- Mengubah `composer.json` tanpa alasan jelas.
- Mengubah `package-lock.json` atau `composer.lock` jika tidak diperlukan.

---

## 💬 Bahasa Respons
- Gunakan **Bahasa Indonesia** untuk penjelasan kepada developer/user.
- Gunakan **Bahasa Inggris** untuk kode, nama class, method, komentar teknis, dan commit message.
