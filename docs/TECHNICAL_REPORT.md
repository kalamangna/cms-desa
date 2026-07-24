# LAPORAN TEKNIS & DOKUMEN SPESIFIKASI SISTEM (SRS & SDD)
## PENGEMBANGAN SISTEM INFORMASI PORTAL DESA & CMS BERBASIS DATA MIKRO

---

**Nama Aplikasi**: Portal Resmi Website & CMS Desa  
**Versi Sistem**: 1.8.2 (Release Production)  
**Kerangka Kerja (Framework)**: Laravel 12, Filament v4, Tailwind CSS v4  
**Penyusun / Pranata Komputer**: Tim Pengembang & Pengelola Sistem Informasi Desa  
**Tanggal Penyusunan**: 24 Juli 2026  

---

## BAB I: PENDAHULUAN & SPESIFIKASI KEBUTUHAN (SRS)

### 1.1 Latar Belakang
Pemerintah Desa memerlukan platform portal informasi digital yang tidak hanya berfungsi sebagai media publikasi berita dan pengumuman, melainkan juga mampu mengelola dan menyajikan data mikro kependudukan (Regsosek & SDGs Desa) secara transparan, akurat, dan real-time. Untuk mendukung hal tersebut, dikembangkan **Sistem Informasi Portal Desa & CMS Berbasis Data Mikro** yang mengintegrasikan pengelolaan data kependudukan tingkat keluarga dan individu dengan visualisasi grafik interaktif.

### 1.2 Tujuan Sistem
1. Menyediakan portal publik desa yang modern, responsif, dan mudah diakses oleh masyarakat.
2. Mengelola data mikro kependudukan terstruktur (`Dusun` $\rightarrow$ `Keluarga` $\rightarrow$ `Penduduk`) melalui Admin Panel terintegrasi.
3. Menyajikan visualisasi data statistik sektoral 1 arah dan 2 arah (cross-tabulation dinamis) yang didukung ekspor data resmi (CSV, Excel, PDF).
4. Menyediakan layanan mandiri permohonan surat, pengaduan warga, serta transparansi realisasi APBDes secara interaktif.

### 1.3 Kebutuhan Fungsional (Functional Requirements)
- **FR-01 (Manajemen Data Mikro)**: Sistem dapat mencatat dan mengimpor data kuesioner keluarga (bangunan, sanitasi, listrik, aset) dan individu warga (pendidikan, pekerjaan, BPJS, PIP, disabilitas) dari berkas Excel.
- **FR-02 (Statistik & Visualisasi 2 Arah)**: Sistem dapat menampilkan grafik batang tumpuk horizontal (*Horizontal Stacked Bar*) dan tabel rincian dengan pembanding dinamis (*Gender*, *Pendidikan*, *Pekerjaan*, *Dusun*, dll.) yang dapat dikonfigurasi dari admin panel.
- **FR-03 (Transparansi APBDes)**: Sistem dapat menampilkan alokasi pendapatan, belanja, dan pembiayaan desa lengkap dengan *progress bar* pencapaian realisasi.
- **FR-04 (Layanan Publik & Mandiri)**: Sistem menyediakan pengajuan permohonan surat dengan nomor tiket pelacakan, formulir pengaduan warga online, serta buku tamu digital.
- **FR-05 (Ekspor Berkas Resmi)**: Sistem menyediakan fungsi ekspor tabel statistik ke format CSV, Excel (XLSX), dan PDF lengkap dengan Kop Header Resmi Pemerintah Desa.

### 1.4 Kebutuhan Non-Fungsional (Non-Functional Requirements)
- **NFR-01 (Security)**: Proteksi CSRF pada semua form, sanitasi input dari ancaman XSS, otorisasi berbasis peran (Spatie Permission), dan enkripsi password mutakhir.
- **NFR-02 (Performance)**: Optimasi query database dengan *Eager Loading* untuk mencegah masalah *N+1 Query*, caching statistik pengunjung, dan minifikasi HTML otomatis.
- **NFR-03 (Usability & Responsiveness)**: Tampilan antarmuka modern (*Glassmorphism Design*) yang responsif di perangkat Mobile, Tablet, maupun Desktop.
- **NFR-04 (SEO Optimization)**: Implementasi Skema Markup JSON-LD (Organization, WebSite, Breadcrumbs, Article) dan Meta Tag terstruktur.

---

## BAB II: PERANCANGAN ARSITEKTUR & BASIS DATA (SDD)

### 2.1 Arsitektur Perangkat Lunak
Sistem dibangun menggunakan arsitektur **Model-View-Controller (MVC)** modern berbasis Laravel 12 dengan pemisahan komponen sebagai berikut:
- **Backend Framework**: Laravel 12 (PHP 8.3+)
- **Admin Panel Engine**: Filament v4
- **Frontend Styling**: Tailwind CSS v4 & Alpine.js
- **Visualisasi & Grafik**: ApexCharts & Leaflet.js (GIS)

```
[ Pengunjung / Warga ]         [ Operator Desa ]
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

### 2.2 Skema Entitas Basis Data Utama (ERD)
1. **`dusuns`**: Menyimpan wilayah administratif dusun ID, Nama Dusun, Kepala Dusun.
2. **`families`**: Menyimpan data kepala keluarga, alamat, Karakteristik Rumah (bangunan, sanitasi, meteran listrik, kepemilikan aset), dan program bantuan sosial (PKH, BPNT, BLT, dll).
3. **`citizens`**: Menyimpan data warga individu (NIK, Nama, Jenis Kelamin, Hubungan Keluarga, Pekerjaan, Pendidikan, Status Perkawinan, BPJS, PIP, Dompet Digital).
4. **`statistic_categories`**: Kategori statistik (`slug`, `name`, `mapping_table`, `secondary_columns` [JSON]).
5. **`statistic_indicators`**: Indikator rincian statistik (`category_id`, `name`, `unit`).
6. **`budget_realizations`**: Data pendapatan dan belanja desa (Tahun, Kategori, Anggaran, Realisasi).

---

## BAB III: IMPLEMENTASI FITUR UNGGULAN & ALGORITMA

### 3.1 Algoritma Cross-Tabulation Statistik Dinamis (`StatisticService.php`)
Sistem menghitung matriks perbandingan 2 arah secara dinamis pada runtime untuk menghindari penyimpanan redundan di database.
- **Logika Matriks**:
  Perhitungan `breakdowns` dilakukan dengan mengelompokkan data warga/keluarga berdasarkan indikator utama dan kolom pembanding sumbu ke-2 (`secondary_columns`).
- **Skema Penguncian Kolom Tabel (Sticky Column)**:
  Untuk kenyamanan navigasi tabel lebar di layar HP/Tablet, sel kolom `Indikator` diterapkan kelas CSS `sticky left-0` dengan warna latar belakang solid 100% (`bg-white` / `bg-slate-50`), *z-index* tinggi (`z-10`/`z-20`), serta garis bayangan pemisah (`shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]`).

### 3.2 Tampilan Horizontal Stacked Bar Chart
Untuk mencegah label indikator yang panjang (misal: *"Buruh / Karyawan / Pegawai Swasta"*) terpotong atau tertulis miring pada grafik 2 arah yang padat, visualisasi disajikan dalam bentuk **Horizontal Stacked Bar Chart** dengan konfigurasi tinggi dinamis (`Math.max(380, count * 48)px`).

---

## BAB IV: PENGUJIAN & VERIFIKASI SISTEM (UAT)

### 4.1 Hasil Pengujian Otomatis (Automated Testing)
Pengujian suite menggunakan PHPUnit / Laravel Pest:
- **Test Case**: `Tests\Feature\StatisticDashboardTest`
- **Status**: **PASS (100%)** — 3 Assertions Lulus.

### 4.2 Matrix Pengujian Fitur Utamanya
| No | Komponen Uji | Skenario | Hasil Diharapkan | Status |
|:---|:---|:---|:---|:---:|
| 1 | Impor Data Mikro | Upload berkas Excel Kependudukan (300+ kolom) | Data Keluarga & Penduduk tersimpan rapi dan di-normalize ke *Title Case* | SUCCESS |
| 2 | Opsi Pembanding Dinamis | Centang opsi pembanding di Filament Admin | Dropdown pembanding muncul di publik & merevisi grafik ApexCharts secara instant | SUCCESS |
| 3 | Ekspor Kop Resmi | Klik Ekspor (CSV, Excel, PDF) | File terunduh berisi Kop Header Resmi Pemerintah Desa & data tabel aktif | SUCCESS |
| 4 | Responsivitas Tabel | Scroll horizontal tabel statistik di HP | Kolom Indikator tetap terkunci (sticky) tanpa ada teks yang bocor/overflow | SUCCESS |

---

## BAB V: KESIMPULAN & REKOMENDASI

### 5.1 Kesimpulan
Sistem Informasi Portal Desa & CMS Berbasis Data Mikro versi 1.8.2 telah selesai dibangun dan diuji dengan hasil sempurna. Sistem ini memenuhi seluruh standar rekayasa perangkat lunak pemerintah (PSR-12, SOLID, Clean Code) serta siap dioperasikan secara penuh untuk mendukung transparansi dan tata kelola pemerintah desa berbasis data akurat.

---
*Dokumen ini merupakan dokumentasi teknis resmi Sistem Informasi Desa.*
