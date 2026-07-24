# 🏛️ Website Desa — Instruksi Pengembangan & Catatan Kerja (Gemini Agent)

## 📌 Aturan Eksekusi Perintah "PUSH" (Pre-Push Checklist)
*   **Pencatatan Perubahan:** Setiap sebelum melakukan *push*, pastikan untuk mencatat semua perubahan yang telah dilakukan di dalam berkas [docs/CHANGELOG.md](file:///Users/abedzul/Desktop/htdocs/desa-cms/docs/CHANGELOG.md) sesuai dengan format Keep a Changelog.
*   **Pembaruan Dokumentasi:** Perbarui berkas [README.md](file:///Users/abedzul/Desktop/htdocs/desa-cms/README.md) jika terdapat perubahan atau penambahan fitur baru yang memerlukan instruksi/konfigurasi tambahan.
*   **Kompilasi CSS (Tailwind 4 & Vite):** Jika terdapat perubahan pada berkas CSS, pastikan untuk melakukan proses *build* CSS (`npm run build`) sebelum melakukan *push*. Namun, karena pengguna saat ini menjalankan proses *watch* CSS (`npm run dev` atau `composer dev`) secara aktif, **tidak perlu menjalankan build CSS secara berulang-ulang** selama penulisan kode maupun sebelum melakukan *push*.
*   **Dependensi Composer (Laravel 12 & Filament v4):** Jika terdapat perubahan pada berkas [composer.json](file:///Users/abedzul/Desktop/htdocs/desa-cms/composer.json) atau `composer.lock`, pastikan untuk menjalankan perintah `composer install --no-dev --optimize-autoloader` sebelum melakukan *push*.
*   **Waktu Eksekusi Aturan:** Seluruh aturan di atas (kompilasi CSS jika diperlukan, pencatatan di CHANGELOG, pembaruan dokumentasi, dll.) **hanya dijalankan jika ada instruksi "push" eksplisit dari pengguna**, bukan secara otomatis di setiap perubahan kode individu.

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
