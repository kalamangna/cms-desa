# BAB IV: IMPLEMENTASI SISTEM (4_IMPLEMENTATION.md)

Dokumen ini menjelaskan rincian implementasi teknis, struktur pengkodean, algoritma, serta solusi rekayasa perangkat lunak yang diterapkan pada **Portal Resmi Website & CMS Desa Versi 1.8.2**.

---

## 4.1 Tumpukan Teknologi & Dependensi (Tech Stack & Environment)

Sistem diimplementasikan mengacu pada standar *Laravel Best Practices* dan *SOLID Principles* menggunakan tumpukan teknologi utama:

| Komponen | Teknologi / Pustaka | Fungsi Utamanya |
|:---|:---|:---|
| **Core Framework** | Laravel 12.x (PHP 8.3+) | Arsitektur backend MVC, routing, Eloquent ORM, dan middleware security. |
| **Admin Panel Engine** | Filament v4 | Panel kontrol operasional desa (CRUD kependudukan, berita, APBDes, dll). |
| **CSS Engine** | Tailwind CSS v4 | Styling komponen antarmuka publik responsif & modern (*Glassmorphism*). |
| **Client Interactivity** | Alpine.js v3 | Manipulasi DOM ringan, state management dropdown, & AJAX filtering. |
| **Data Visualization** | ApexCharts.js | Rendering grafik interaktif (Pie, Donut, Bar, & Horizontal Stacked Bar). |
| **Spasial & Pemetaan** | Leaflet.js | Pemetaan wilayah dusun dan sebaran titik koordinat fasilitas desa. |
| **Excel Processing** | PhpSpreadsheet v5 | Pembacaan dan ekstraksi berkas Excel kuesioner kependudukan (300+ kolom). |
| **Hak Akses & RBAC** | Spatie Laravel-Permission | Pengelolaan peran (Super Admin, Operator Desa, Redaktur Berita). |

---

## 4.2 Implementasi Core Kependudukan & Statistik

### 4.2.1 Service Layer Matriks Cross-Tabulation (`StatisticService.php`)
Untuk menghindari duplikasi dan penyimpanan matriks data redundan pada basis data, perhitungan perbandingan 2 arah (*cross-tabulation*) dilakukan secara dinamis pada runtime menggunakan `StatisticService`.
- **Pemeriksaan Opsi Pembanding (`secondary_columns`)**: Sistem mengekstrak kolom pembanding yang diizinkan dari atribut JSON `secondary_columns` pada tabel `statistic_categories`.
- **Kueri Data Mikro Ter-Normalisasi**: Data warga/keluarga difilter berdasarkan `dusun_id` dan `year`.
- **Penyusunan Matriks `breakdowns`**: Sistem mengelompokkan jumlah warga per indikator utama terhadap setiap opsi nilai pembanding (misal: *Petani/Pekebun* $\rightarrow$ *Laki-laki*: 450, *Perempuan*: 210).

### 4.2.2 Form Admin Panel Filament v4 (Data Mikro)
Untuk memudahkan Operator Desa mengelola data mikro kependudukan yang sangat banyak (300+ atribut), form input Filament dikelompokkan ke dalam skema **Tab & Sub-Section** yang terorganisir:
- **Skema Form Penduduk (`CitizenResource`)**: Terbagi menjadi 5 Tab Utama (Identitas Warga, Pendidikan & Pekerjaan, Pendapatan & Keuangan, Kesehatan & Disabilitas, dan Kependudukan).
- **Skema Form Keluarga (`FamilyResource`)**: Terbagi menjadi 4 Tab Utama (Identitas Keluarga, Karakteristik Rumah, Sanitasi & Utilitas, dan Aset & Bantuan).

### 4.2.3 Visualisasi Stacked Bar 2 Arah Dinamis
Pada grafik 2 arah berbasis Bar Chart vertikal, label profesi yang panjang sering tumpang tindih. Sebagai solusinya, saat pembanding 2 arah diaktifkan, grafik secara otomatis dialihkan ke mode **Horizontal Stacked Bar Chart** (`horizontal: true`). Tinggi elemen kontainer dihitung secara dinamis pada Alpine.js (`Math.max(380, count * 48)`) memastikan batang horizontal memiliki ruang cukup.

### 4.2.4 Penguncian Sel Tabel (*Sticky Column Solid*)
Untuk mencegah *overflow* visual saat pengguna melakukan *scroll* horizontal pada tabel statistik, sel kolom `Indikator` diterapkan CSS posisi `sticky left-0` dengan warna *background* solid 100% dan bayangan pemisah (`shadow`) sebagai pembatas visual yang elegan.

---

## 4.3 Implementasi Modul CMS & Profil Desa

### 4.3.1 Manajemen Konten (CMS)
- **Berita & Pengumuman**: Publikasi artikel (`posts`) dikelompokkan berdasarkan relasi `categories` dan terintegrasi dengan pengelolaan media gambar.
- **Galeri & Infografis Popup**: Disediakan modul khusus untuk mengontrol materi visual dan *banner* dinamis pada halaman publik secara efisien.

### 4.3.2 Profil Lembaga, Aparatur, dan Potensi Desa
Entitas profil pemerintahan (Lembaga, Aparatur) serta Potensi Desa diimplementasikan menggunakan arsitektur CRUD terpisah. Modul ini mendukung manajemen urutan tampil (`order`) dan penyematan struktur hierarki kepengurusan desa.

### 4.3.3 Pemetaan GIS Fasilitas Publik
Manajemen Fasilitas Publik (`public_facilities`) diimplementasikan dengan sistem informasi geografis (GIS) ringan yang merekam titik koordinat spasial (Latitude & Longitude). Titik-titik tersebut divisualisasikan menjadi penanda (*marker*) pada peta interaktif menggunakan *library* **Leaflet.js**.

---

## 4.4 Implementasi Layanan Publik & Keterbukaan

### 4.4.1 Layanan Mandiri & Pengaduan Warga
- **Generator Nomor Tiket Unik**: Sistem otomatis men-generate ID pelacakan seperti `SRT-YYYYMMDD-XXXX` (untuk permohonan surat) dan `ADU-YYYYMMDD-XXXX` (untuk pengaduan).
- **Sistem Pelacakan Status**: Warga dapat melacak progress verifikasi surat atau penanganan pengaduan secara *real-time* dengan memasukkan nomor tiket pada antarmuka publik.

### 4.4.2 Keterbukaan Publik & Transparansi APBDes
- **Dokumen & Dataset Terbuka**: Repositori `documents` dan `datasets` diimplementasikan untuk menyediakan data yang bisa diakses publik secara bebas, didukung fitur pelacakan unduhan (`download_count`).
- **Visualisasi APBDes**: Menyajikan ringkasan realisasi APBDes desa yang dirender ke dalam grafik *progress bar* perbandingan antara nilai Anggaran dan Realisasi.

---

## 4.5 Implementasi Optimasi & SEO

### 4.5.1 Kompresi Gambar WhatsApp Client-Side
- Form *upload* berita memproses *resizing* secara instan (*client-side*) via Canvas HTML5. Foto berukuran besar otomatis di-*resize* ke dimensi ideal **1200 x 630 piksel** dengan kualitas 80% (ukuran akhir < 300 KB). Pratinjau tautan WhatsApp menjadi tampil sempurna.

### 4.5.2 Schema Markup JSON-LD
Seluruh halaman disuntikkan metadata terstruktur schema.org (`Organization`, `WebSite`, `BreadcrumbList`, dan `Article`) dalam format JSON-LD untuk mendorong performa *Search Engine Optimization* (SEO).

---

## 4.6 Implementasi Konfigurasi & Keamanan Sistem

### 4.6.1 Pencatatan Log Interaksi
- **Buku Tamu (Guest Book) & Log Pengunjung**: Sistem merekam setiap agenda interaksi fisik melalui buku tamu digital serta mencatat riwayat kunjungan *traffic* (`visitor_logs`) berdasarkan *IP Address* dan *User Agent*.

### 4.6.2 Pengaturan Global Sistem (Settings & Metadata)
Semua aset dinamis sistem (seperti nama desa, logo, kontak, maupun tag SEO kustom) disimpan secara terstruktur menggunakan arsitektur *Key-Value Pair* (`settings` dan `metadata`). Model arsitektur ini membebaskan sistem dari *hardcoding*, sehingga operator dapat mengubah konfigurasi *web* dari Admin Panel tanpa menyentuh kode program.

### 4.6.3 Manajemen Pengguna (RBAC)
Otorisasi otentikasi diimplementasikan dengan `spatie/laravel-permission` yang membangi tingkatan hak akses berbasis *Role-Based Access Control*, meliputi tingkat kewenangan Super Admin, Operator Data Mikro, dan Redaktur Berita.
