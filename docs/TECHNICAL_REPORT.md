# LAPORAN TEKNIS PENGEMBANGAN SISTEM INFORMASI PORTAL DESA & CMS BERBASIS DATA MIKRO

---

## 📑 COVER

```
================================================================================
                    LAPORAN REKAYASA PERANGKAT LUNAK
       PENGEMBANGAN SISTEM INFORMASI PORTAL DESA & CMS BERBASIS DATA MIKRO
                           VERSI PRODUCTION 1.8.2
================================================================================

                                Disusun Oleh:
                     Jabatan Fungsional Pranata Komputer
                   Tim Pengembang & Pengelola Sistem Desa

                               Pemerintah Desa
                                 Tahun 2026
```

---

## ✍️ LEMBAR PENGESAHAN

```
                       LEMBAR PENGESAHAN LAPORAN TEKNIS
       SISTEM INFORMASI PORTAL DESA & CMS BERBASIS DATA MIKRO (v1.8.2)

   Naskah Laporan Rekayasa Perangkat Lunak ini telah diperiksa, diuji, dan
   disetujui sebagai Dokumen Teknis Resmi untuk keperluan Penilaian Angka Kredit
   (PAK) Jabatan Fungsional Pranata Komputer serta Dokumen Serah Terima Operasional.

   Disetujui di : Desa
   Pada Tanggal : 24 Juli 2026

   Mengetahui,                                       Disusun Oleh,
   Kepala Desa                                       Pranata Komputer

   ( ____________________ )                          ( ____________________ )
```

---

## 📜 KATA PENGANTAR

Puji dan syukur kami panjatkan ke hadirat Tuhan Yang Maha Esa atas rahmat dan karunia-Nya, sehingga Laporan Teknis Pengembangan **Sistem Informasi Portal Desa & CMS Berbasis Data Mikro Versi 1.8.2** ini dapat diselesaikan dengan baik.

Dokumen ini disusun untuk memberikan gambaran secara menyeluruh mengenai proses rekayasa perangkat lunak, mulai dari tahap analisis kebutuhan, perancangan arsitektur, struktur basis data, pengembangan API, prosedur pengujian (*testing*), hingga panduan pemeliharaan (*maintenance*).

Penyusun mengucapkan terima kasih kepada seluruh pihak yang telah memberikan dukungan dan kontribusi selama proses pengembangan sistem informasi ini. Semoga sistem informasi ini dapat memberikan manfaat nyata bagi transparansi dan efektivitas tata kelola pemerintahan desa.

*Desa, 24 Juli 2026*  
**Tim Penyusun / Pranata Komputer**

---

## 📖 DAFTAR ISI

- **COVER**
- **LEMBAR PENGESAHAN**
- **KATA PENGANTAR**
- **DAFTAR ISI**
- **BAB I: PENDAHULUAN**
  - 1.1 Latar Belakang
  - 1.2 Maksud dan Tujuan
  - 1.3 Ruang Lingkup
- **BAB II: ANALISIS KEBUTUHAN**
  - 2.1 Kebutuhan Fungsional (*Functional Requirements*)
  - 2.2 Kebutuhan Non-Fungsional (*Non-Functional Requirements*)
- **BAB III: PERANCANGAN SISTEM**
  - 3.1 Arsitektur Perangkat Lunak
  - 3.2 Diagram Alir Data (*Data Flow*)
- **BAB IV: IMPLEMENTASI**
  - 4.1 Teknologi & Pustaka Pendukung
  - 4.2 Fitur Visualisasi Stacked Bar 2 Arah Dinamis
  - 4.3 Penguncian Kolom Tabel (*Sticky Column*)
- **BAB V: DATABASE**
  - 5.1 ERD & Relasi Entitas
  - 5.2 Spesifikasi Tabel Utama
- **BAB VI: API**
  - 6.1 Endpoint Statistik Dinamis (`GET /statistik`)
  - 6.2 Endpoint Layanan Mandiri & Pengaduan Warga
- **BAB VII: PANDUAN INSTALASI**
  - 7.1 Pemasangan Lingkungan Lokal (*Development*)
  - 7.2 Deployment Server Produksi (Hostinger/cPanel)
- **BAB VIII: PANDUAN PENGGUNA**
  - 8.1 Petunjuk Operasional Admin Panel (Filament)
  - 8.2 Petunjuk Penggunaan Portal Publik
- **BAB IX: PENGUJIAN**
  - 9.1 Hasil Pengujian Otomatis (*Automated Testing*)
  - 9.2 Pengujian Penerimaan Pengguna (*User Acceptance Testing*)
- **BAB X: PEMELIHARAAN**
  - 10.1 Solusi Kendala Hosting & Storage Link
  - 10.2 Hardening Keamanan & Optimasi Cache
- **LAMPIRAN**
  - Lampiran A: Riwayat Perubahan (*Changelog*)
  - Lampiran B: Tangkapan Layar Tampilan (*Screenshot*)
  - Lampiran C: Diagram Diagram Relasi Basis Data (*ERD*)
  - Lampiran D: Struktur Folder Proyek (*Directory Tree*)

---

## BAB I: PENDAHULUAN

### 1.1 Latar Belakang
Pemerintah Desa memerlukan portal informasi digital yang tidak hanya berfungsi sebagai media berita dan pengumuman, melainkan juga mampu mengelola dan menyajikan data sosial ekonomi mikro kependudukan (Regsosek & SDGs Desa) secara transparan, akurat, dan *real-time*. Oleh karena itu, dikembangkan **Sistem Informasi Portal Desa & CMS Berbasis Data Mikro** yang mengintegrasikan pengelolaan data tingkat keluarga dan individu dengan visualisasi grafik interaktif.

### 1.2 Maksud dan Tujuan
1. Menyediakan portal publik desa yang modern, responsif, dan mudah diakses oleh masyarakat.
2. Mengelola data mikro kependudukan terstruktur (`Dusun` $\rightarrow$ `Keluarga` $\rightarrow$ `Penduduk`) via Admin Panel terintegrasi.
3. Menyajikan visualisasi data statistik sektoral 1 arah dan 2 arah (*cross-tabulation* dinamis) yang didukung ekspor data resmi (CSV, Excel, PDF).
4. Menyediakan layanan mandiri permohonan surat, pengaduan warga, serta transparansi realisasi APBDes secara interaktif.

### 1.3 Ruang Lingkup
Ruang lingkup proyek mencakup pembangunan backend Laravel 12, admin panel Filament v4, antarmuka publik Tailwind CSS v4, visualisasi grafik ApexCharts, pemetaan wilayah Leaflet.js, serta mekanisme pengujian & deployment.

---

## BAB II: ANALISIS KEBUTUHAN

### 2.1 Kebutuhan Fungsional (Functional Requirements)
- **FR-01 (Manajemen Data Mikro)**: Mengimpor dan mengelola kuesioner keluarga (bangunan, sanitasi, listrik, aset) dan individu warga (pendidikan, pekerjaan, BPJS, PIP, disabilitas) dari berkas Excel.
- **FR-02 (Statistik 2 Arah Dinamis)**: Menyajikan grafik *Horizontal Stacked Bar* dan tabel rincian dengan pembanding dinamis (*Gender*, *Pendidikan*, *Pekerjaan*, *Dusun*, dll).
- **FR-03 (Transparansi APBDes)**: Menyajikan target dan realisasi pendapatan/belanja desa lengkap dengan *progress bar* pencapaian.
- **FR-04 (Layanan Mandiri & Pengaduan)**: Menyediakan permohonan surat online ber-nomor tiket, formulir pengaduan warga, dan buku tamu digital.
- **FR-05 (Ekspor Berkas Resmi)**: Menyediakan ekspor tabel statistik ke format CSV, Excel (XLSX), dan PDF ber-Kop Header Resmi Pemerintah Desa.

### 2.2 Kebutuhan Non-Fungsional (Non-Functional Requirements)
- **NFR-01 (Security)**: Proteksi CSRF, sanitasi input XSS, otorisasi peran (Spatie Permission), dan enkripsi password Bcrypt/Argon2.
- **NFR-02 (Performance)**: Optimasi kueri dengan *Eager Loading* untuk mencegah masalah *N+1 Query*, caching statistik pengunjung, dan minifikasi HTML.
- **NFR-03 (Responsiveness)**: Desain antarmuka modern yang responsif di Mobile, Tablet, dan Desktop.

---

## BAB III: PERANCANGAN SISTEM

### 3.1 Arsitektur Perangkat Lunak
Sistem dibangun menggunakan pola **Model-View-Controller (MVC)** modern berbasis Laravel 12:
- **Backend Framework**: Laravel 12 (PHP 8.3+)
- **Admin Panel Engine**: Filament v4
- **Frontend Styling**: Tailwind CSS v4 & Alpine.js
- **Visualisasi & Pemetaan**: ApexCharts & Leaflet.js

### 3.2 Diagram Alir Data (Data Flow)
```
[ Warga / Publik ]            [ Operator Desa ]
        │                            │
        ▼                            ▼
┌──────────────────┐        ┌──────────────────┐
│   Web Frontend   │        │ Filament Admin   │
│  (Blade + Alpine)│        │   (Control Panel)│
└────────┬─────────┘        └────────┬─────────┘
         │                           │
         └─────────────┬─────────────┘
                       ▼
           ┌──────────────────────┐
           │  Laravel Core Engine │
           │  (Routes, Controller,│
           │   StatisticService)  │
           └───────────┬──────────┘
                       ▼
           ┌──────────────────────┐
           │ Database (MySQL/Maria)│
           └──────────────────────┘
```

---

## BAB IV: IMPLEMENTASI

### 4.1 Teknologi & Pustaka Pendukung
Sistem mengintegrasikan berbagai teknologi mutakhir:
- **Laravel 12 & Filament v4**: Pengelolaan data CRUD dan manajemen otorisasi admin.
- **Tailwind CSS v4 & Alpine.js**: Styling komponen antarmuka publik dan manipulasi DOM dinamis.
- **ApexCharts**: Rendering grafik batang tumpuk horizontal (*Horizontal Stacked Bar*) secara responsif.

### 4.2 Fitur Visualisasi Stacked Bar 2 Arah Dinamis
Ketika pembanding 2 arah diaktifkan (misal: *Pekerjaan x Pendidikan*), grafik secara otomatis dikunci dalam mode **Horizontal Stacked Bar Chart** dengan kalkulasi tinggi dinamis (`Math.max(380, count * 48)px`) agar label profesi berdiri tegak lurus dan rapi.

### 4.3 Penguncian Kolom Tabel (Sticky Column)
Kolom `Indikator` pada `<thead>`, `<tbody>`, dan `<tfoot>` diterapkan kelas CSS `sticky left-0` dengan warna background solid 100% (`bg-white` / `bg-slate-50`), *z-index* tinggi (`z-10`/`z-20`), dan bayangan pemisah (`shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]`) agar data tidak bocor saat tabel di-scroll.

---

## BAB V: DATABASE

### 5.1 ERD & Relasi Entitas
Relasi antar tabel kependudukan: `dusuns` (1:N) $\rightarrow$ `families` (1:N) $\rightarrow$ `citizens`.

### 5.2 Spesifikasi Tabel Utama
- **`dusuns`**: `id`, `name`, `head_name`.
- **`families`**: `id`, `dusun_id`, `family_card_number`, `head_of_family_name`, `address`, `building_type`, `ownership_status`, `electricity_power_meter_1..3`, `assistance_type`, `motorcycle_value`, `car_value`.
- **`citizens`**: `id`, `family_id`, `dusun_id`, `nik`, `name` (UPPERCASE), `gender`, `family_relationship`, `education_level`, `job`, `job_status`, `marital_status`, `bpjs_status`, `pip_status`, `has_digital_wallet`, `domicile_address_type`.
- **`statistic_categories`**: `id`, `name`, `slug`, `mapping_table`, `secondary_columns` (JSON).

---

## BAB VI: API

### 6.1 Endpoint Statistik Dinamis (`GET /statistik`)
- **Query Parameters**: `kategori`, `dusun_id`, `year`, `json=1`.
- **Respon JSON**: Mengembalikan data kategori, `secondaryConfigs`, array indikator, dan matriks `breakdowns` per indikator.

### 6.2 Endpoint Layanan Mandiri & Pengaduan Warga
- `POST /layanan-mandiri/permohonan`: Pengajuan permohonan surat warga (menghasilkan Nomor Tiket SRT-YYYYMMDD-XXXX).
- `POST /pengaduan`: Formulir pengaduan warga online.

---

## BAB VII: PANDUAN INSTALASI

### 7.1 Pemasangan Lingkungan Lokal (Development)
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

### 7.2 Deployment Server Produksi (Hostinger/cPanel)
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

---

## BAB VIII: PANDUAN PENGGUNA

### 8.1 Petunjuk Operasional Admin Panel (Filament)
- **Login**: Akses `/admin`, masukkan kredensial operator.
- **Impor Excel**: Masuk menu Kependudukan $\rightarrow$ Klik Impor Excel $\rightarrow$ Unggah file kuesioner.
- **Set Opsi Pembanding**: Masuk Master $\rightarrow$ Kategori Statistik $\rightarrow$ Centang `secondary_columns`.

### 8.2 Petunjuk Penggunaan Portal Publik
- **Statistik**: Akses `/statistik` $\rightarrow$ Pilih Kategori $\rightarrow$ Ubah Dropdown Pembanding $\rightarrow$ Ekspor CSV/XLSX/PDF.
- **Layanan Mandiri**: Akses `/layanan-mandiri` $\rightarrow$ Input NIK & Jenis Surat $\rightarrow$ Dapatkan Nomor Tiket.

---

## BAB IX: PENGUJIAN

### 9.1 Hasil Pengujian Otomatis (Automated Testing)
Pengujian suite menggunakan PHPUnit / Pest:
- **Test Case**: `Tests\Feature\StatisticDashboardTest`
- **Hasil**: **PASS (100%)** — 3 Assertions Lulus.

### 9.2 Pengujian Penerimaan Pengguna (User Acceptance Testing)
| No | Fitur Uji | Skenario | Hasil | Status |
|:---|:---|:---|:---|:---:|
| 1 | Impor Data Mikro | Upload Excel 300+ Kolom | Data tersimpan & ter-normalize *Title Case* | SUCCESS |
| 2 | Opsi Pembanding | Centang opsi pembanding di Filament | Dropdown & Grafik Stacked Bar ter-update | SUCCESS |
| 3 | Ekspor Kop Resmi | Klik Ekspor PDF/Excel | File terunduh ber-Kop Resmi Desa | SUCCESS |
| 4 | Sticky Table Cell | Scroll horizontal tabel | Kolom Indikator terkunci tanpa overflow | SUCCESS |

---

## BAB X: PEMELIHARAAN

### 10.1 Solusi Kendala Hosting & Storage Link
Jika gambar media tidak muncul di Hostinger (akibat `symlink()` ditutup), jalankan pembuat symlink via SSH: `ln -s ~/cms-desa/public ~/public_html`.

### 10.2 Hardening Keamanan & Optimasi Cache
Jalankan pembersihan cache rutin:
```bash
php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan cache:clear
```

---

## LAMPIRAN

### Lampiran A: Riwayat Perubahan (Changelog Summary)
- **v1.8.2**: Fitur Multi-Pembanding Dinamis 2 Arah, Horizontal Stacked Bar Chart, Sticky Column Solid, dan Konsolidasi Dokumen.
- **v1.8.1**: Reorganisasi 5 Tab Form Penduduk dan 4 Tab Form Keluarga.
- **v1.8.0**: Multi-Program Bantuan Sosial CheckboxList & Normalisasi Status Pekerjaan Title Case.

### Lampiran B: Tangkapan Layar Tampilan (Screenshot)
- *Dashboard Statistik Public* (`/statistik`)
- *Filament Admin Panel* (`/admin`)
- *Halaman Transparansi APBDes* (`/apbdes`)

### Lampiran C: Diagram Relasi Basis Data (ERD)
`dusuns` (1:N) $\rightarrow$ `families` (1:N) $\rightarrow$ `citizens`.

### Lampiran D: Struktur Folder Proyek (Directory Tree)
```
cms-desa/
├── app/
│   ├── Filament/Resources/
│   ├── Models/
│   └── Services/StatisticService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── docs/
│   ├── TECHNICAL_REPORT.md
│   ├── SYSTEM_REQUIREMENTS.md
│   ├── SYSTEM_DESIGN.md
│   ├── DATABASE.md
│   ├── API.md
│   ├── INSTALLATION_GUIDE.md
│   ├── USER_GUIDE.md
│   ├── TESTING_REPORT.md
│   ├── MAINTENANCE_GUIDE.md
│   └── CHANGELOG.md
├── resources/views/
│   └── statistics/index.blade.php
├── routes/web.php
└── README.md
```
