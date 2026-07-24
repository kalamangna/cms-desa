# 🏛️ Portal Resmi Website Desa

Portal Informasi Desa Modern, Transparan, dan Berbasis Data Mikro. Menyajikan visualisasi data sosial ekonomi (Regsosek/SDGs Desa) secara interaktif.

---


## 🚀 Fitur Utama

- **Dashboard Statistik Interaktif**: Grafik & diagram data kependudukan, pekerjaan, pendidikan, disabilitas, dan penyakit kronis secara real-time yang dihitung dari database warga mikro. Supports **Horizontal Stacked Bar Chart** untuk perbandingan 2 arah.
- **Transparansi APBDes**: Visualisasi realisasi anggaran pendapatan, belanja, dan pembiayaan desa lengkap dengan diagram donat alokasi dan progress bar pencapaian.
- **Portal Informasi & Publikasi Desa**:
  - **Berita & Kegiatan**: Publikasi artikel berita dengan fitur SEO meta lengkap & schema JSON-LD, serta kompresi otomatis pratinjau WhatsApp.
  - **Pengumuman Resmi**: Daftar pengumuman terintegrasi dengan fitur **Baca Cepat** berbasis accordion.
  - **Galeri Multi-tipe**: Galeri visual terintegrasi untuk foto kegiatan dan video tautan YouTube.
  - **Lembaga Desa**: Halaman profil lembaga kemasyarakatan desa (`/lembaga`) terstruktur dalam grid modern dengan logo opsional.
  - **Arsip Dokumen**: Portal unduhan keputusan kepala desa, peraturan desa, dan dokumen administrasi publik lainnya.
  - **Potensi Desa**: Galeri dan deskripsi sektor pariwisata, komoditas pertanian/perkebunan, peternakan, industri kreatif, seni & budaya.
- **Data Mikro SDGs & Regsosek**:
  - Model kependudukan mikro terintegrasi: `Dusun` $\rightarrow$ `Keluarga (Family)` $\rightarrow$ `Penduduk (Citizen)`.
  - Panel admin Filament untuk input kuesioner keluarga dan data penduduk.
- **Layanan Mandiri & Pengaduan Warga**: Layanan permohonan surat administrasi desa, pelacakan status permohonan via nomor tiket, formulir pengaduan warga online, serta buku tamu digital terintegrasi.
- **Peta Spasial Desa**: Pemetaan interaktif wilayah administratif dusun, batas wilayah, dan titik koordinat sebaran fasilitas umum berbasis Leaflet.js.

---

## 🛠️ Stack Teknologi

- **Framework**: [Laravel 12](https://laravel.com) (PHP 8.3+)
- **Admin Panel**: [Filament v4](https://filamentphp.com)
- **CSS Engine**: [Tailwind CSS v4](https://tailwindcss.com)
- **Interaktivitas**: Alpine.js, ApexCharts, Leaflet.js
- **Database**: MySQL / MariaDB / PostgreSQL / SQLite / SQL Server

---

## 💻 Ringkasan Cara Menjalankan Aplikasi

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/kalamangna/cms-desa.git
   cd cms-desa
   ```
2. **Install Dependensi & Konfigurasi**:
   ```bash
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   ```
3. **Migrasi & Seed Data**:
   ```bash
   php artisan migrate:fresh --seed
   ```
4. **Kompilasi Aset & Run Dev Server**:
   ```bash
   npm run dev
   php artisan serve
   ```

