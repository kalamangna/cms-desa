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

## 4.2 Implementasi Service Layer Matriks Cross-Tabulation (`StatisticService.php`)

Untuk menghindari duplikasi dan penyimpanan matriks data redundan pada basis data, perhitungan perbandingan 2 arah (*cross-tabulation*) dilakukan secara dinamis pada runtime menggunakan `StatisticService`.

### 💡 Algoritma Perhitungan Matriks:
1. **Pemeriksaan Opsi Pembanding (`secondary_columns`)**:
   Sistem mengekstrak kolom pembanding yang diizinkan dari atribut JSON `secondary_columns` pada tabel `statistic_categories`.
2. **Kueri Data Mikro Ter-Normalisasi**:
   Data warga/keluarga difilter berdasarkan `dusun_id` dan `year`.
3. **Penyusunan Matriks `breakdowns`**:
   Sistem mengelompokkan jumlah warga per indikator utama terhadap setiap opsi nilai pembanding (misal: *Petani/Pekebun* $\rightarrow$ *Laki-laki*: 450, *Perempuan*: 210).

```php
// Potongan Logika Service Layer Perhitungan Matriks Cross-Tabulation
$breakdowns = [];
foreach ($secondaryKeys as $key) {
    if (isset($secondaryConfigs[$key])) {
        $breakdowns[$key] = [];
        foreach ($secondaryConfigs[$key]['options'] as $opt) {
            $breakdowns[$key][$opt] = $query->clone()
                ->where($indicator->mapping_column, $indicator->name)
                ->where($key, $opt)
                ->count();
        }
    }
}
```

---

## 4.3 Implementasi Form Admin Panel Filament v4

Untuk memudahkan Operator Desa mengelola data mikro kependudukan yang sangat banyak (300+ atribut), form input Filament dikelompokkan ke dalam skema **Tab & Sub-Section** yang terorganisir:

### A. Skema Form Penduduk (`CitizenResource` — 5 Tab Utama):
1. 👤 **Identitas Warga**: NIK, Nama Lengkap, Jenis Kelamin, Dusun, Alamat Domisili, & Hubungan Keluarga.
2. 🎓 **Pendidikan & Pekerjaan**: Tingkat Pendidikan, Pekerjaan Utama, Status Pekerjaan, & Keterampilan.
3. 💰 **Pendapatan & Keuangan**: Estimasi Pendapatan, BPJS Ketenagakerjaan, PIP, & Dompet Digital.
4. 🩺 **Kesehatan & Disabilitas**: Status BPJS Kesehatan, Jenis Disabilitas, & Riwayat Penyakit Kronis.
5. 🏠 **Kependudukan**: Status Perkawinan, Agama, Warganegara, & Kesesuaian Alamat Domisili.

### B. Skema Form Keluarga (`FamilyResource` — 4 Tab Utama):
1. 👨‍👩‍👧‍👦 **Identitas Keluarga**: No. KK, Nama Kepala Keluarga, Dusun, RT/RW, & Alamat.
2. 🏠 **Karakteristik Rumah**: Jenis Bangunan, Status Kepemilikan, Material Lantai, Dinding, & Atap.
3. 🚰 **Sanitasi & Utilitas**: Sumber Air Minum, Fasilitas Buang Air Besar, & 3 Meteran Listrik terpisah.
4. 🚗 **Aset & Bantuan**: Nilai Aset Kendaraan (Motor/Mobil) & Multi-Pilihan Program Bantuan Sosial (PKH, BPNT, BLT).

---

## 4.4 Implementasi Visualisasi Stacked Bar 2 Arah Dinamis

### 🎯 Masalah Label Terpotong / Miring:
Pada grafik 2 arah berbasis Bar Chart vertikal biasa, label profesi/indikator yang panjang (misal: *"Buruh Harian Lepas / Pegawai Swasta"*) mengalami tumpang tindih (*overlap*) atau tertulis miring sehingga tidak nyaman dibaca di layar HP/Tablet.

### 🛠️ Solusi Implementasi Grafik:
- **Penguncian Mode Horizontal**: Saat pembanding 2 arah diaktifkan (`activeSecondaryKey != 'none'`), grafik secara otomatis dialihkan ke mode **Horizontal Stacked Bar Chart** (`horizontal: true`).
- **Dynamic Container Height**: Tinggi elemen kontainer grafik dihitung secara dinamis pada Alpine.js:
  ```javascript
  chartHeight = Math.max(380, count * 48);
  ```
  Hal ini memastikan seluruh batang horizontal memiliki ruang yang cukup tanpa saling berhimpitan.
- **ApexCharts Category Injection**: Seluruh nama indikator disuntikkan secara tepat ke atribut `xaxis.categories` yang dipetakan oleh ApexCharts sebagai label Sumbu-Y vertikal.

---

## 4.5 Implementasi Penguncian Sel Tabel (*Sticky Column Solid*)

### 🎯 Masalah Overflow pada Horizontal Scroll:
Saat pengguna melakukan *scroll* horizontal pada tabel statistik perbandingan 2 arah di perangkat seluler, data sel di sebelah kanan tembus pandang atau tertutup oleh teks sel di bawahnya jika kolom `Indikator` tidak dikunci secara solid.

### 🛠️ Solusi CSS Sticky Column:
Pada template `statistics/index.blade.php`, sel kolom `Indikator` diterapkan aturan CSS berikut:
1. **Posisi Sticky**: `sticky left-0` pada `<th>` dan `<td>`.
2. **Solid Background**: Menggunakan warna solid 100% (`bg-white` untuk baris biasa, `bg-slate-50` untuk baris belang, dan `bg-slate-100` untuk baris total/header).
3. **Layering Z-Index**: `z-10` untuk sel bodi dan `z-20` untuk sel header.
4. **Visual Shadow Separator**: Menambahkan bayangan kanan `shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]` sebagai pembatas visual yang elegan.

---

## 4.6 Implementasi Kompresi Gambar WhatsApp & SEO JSON-LD

### A. Kompresi Gambar Berita Client-Side:
- Menggunakan skrip pemrosesan Canvas HTML5 pada form upload berita Filament.
- Foto kamera ponsel berukuran > 5 MB secara otomatis di-resize ke dimensi ideal **1200 x 630 piksel** dengan tingkat kompresi kualitas 80% (ukuran file akhir **< 300 KB**).
- Hasilnya, pratinjau tautan WhatsApp (*WhatsApp Link Preview*) muncul secara instant saat berita dibagikan.

### B. Schema Markup JSON-LD:
Seluruh halaman publik dilengkapi metadata terstruktur schema.org (`Organization`, `WebSite`, `BreadcrumbList`, dan `Article`) yang dirender dalam format JSON-LD untuk memaksimalkan indeks mesin pencari (SEO Google).

---

## 4.7 Implementasi Layanan Mandiri & Pengaduan Warga

Sistem menyediakan modul permohonan surat administrasi desa dan formulir pengaduan online:
- **Generator Nomor Tiket Unik**:
  - Permohonan Surat: `SRT-YYYYMMDD-XXXX` (misal: `SRT-20260724-0042`).
  - Pengaduan Warga: `ADU-YYYYMMDD-XXXX`.
- **Sistem Pelacakan Status**: Warga dapat mengecek progress verifikasi surat atau penanganan pengaduan secara real-time via input nomor tiket pada halaman `/layanan/lacak` dan `/pengaduan/lacak`.
