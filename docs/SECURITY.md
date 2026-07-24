# KEBIJAKAN & HARDENING KEAMANAN (SECURITY.md)

Dokumen ini menjelaskan arsitektur keamanan, kebijakan perlindungan data, dan langkah-langkah *hardening* yang diterapkan pada **Portal Resmi Website & CMS Desa**.

---

## 🛡️ 1. Arsitektur Keamanan Utama

### A. Perlindungan Form (CSRF Protection)
Seluruh formulir publik dan panel admin Filament dilindungi oleh middleware **CSRF (Cross-Site Request Forgery)** bawaan Laravel. Setiap permohonan `POST`, `PUT`, `DELETE` wajib melampirkan Token CSRF yang valid.

### B. Sanitasi Input & Pencegahan XSS (Cross-Site Scripting)
- Seluruh teks input yang dikirim pengguna di-sanitasi secara otomatis.
- Pencetakan teks di Blade Template wajib menggunakan sintaks pembersihan `{{ $variable }}`.
- String HTML pada komponen Alpine.js di-escape secara presisi untuk mencegah injeksi skrip berbahaya.

### C. Pencegahan Injeksi Basis Data (SQL Injection)
Sistem menggunakan **Laravel Eloquent ORM & Prepared Statements** (PDO Binding) untuk seluruh operasi kueri basis data. Tidak ada kueri mentah (*raw query*) yang menerima parameter pengguna secara langsung.

---

## 🔐 2. Autentikasi & Manajemen Hak Akses (RBAC)

- **Otorisasi Berbasis Peran**: Menggunakan pustaka `Spatie/Laravel-Permission` untuk membatasi hak akses pengguna admin.
- **Enkripsi Kata Sandi**: Kata sandi pengguna disimpan menggunakan fungsi hashing aman **Bcrypt / Argon2id**. Tidak ada password yang disimpan dalam bentuk teks polos (*plaintext*).
- **Pembatasan Percobaan Login (Rate Limiting)**: Panel login Filament dilengkapi proteksi pembatasan percobaan login untuk mencegah serangan *Brute Force*.

---

## 📁 3. Keamanan Unggahan Berkas (File Upload Security)

1. **Validasi Ekstensi Berkas**: Unggahan gambar hanya menerima ekstensi `.jpg`, `.jpeg`, `.png`, `.webp`, `.pdf`. Berkas skrip yang berisiko (seperti `.php`, `.exe`, `.sh`, `.js`) secara tegas ditolak oleh sistem.
2. **Validasi Ukuran Berkas**: Sistem membatasi ukuran maksimal file unggahan.
3. **Pemberlakuan HTTPS Global**: Seluruh tautan media dipaksa menggunakan protokol enkripsi `https://` melalui fungsi `URL::forceScheme('https')` pada lingkungan produksi.
