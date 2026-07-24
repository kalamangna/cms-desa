# SYSTEM REQUIREMENTS DOCUMENT (SYSTEM_REQUIREMENTS.md)
## BAB I: PENDAHULUAN & BAB II: ANALISIS KEBUTUHAN

---

## BAB I: PENDAHULUAN

### 1.1 Latar Belakang
Pemerintah Desa memerlukan portal informasi digital yang tidak hanya berfungsi sebagai media publikasi berita dan pengumuman, melainkan juga mampu mengelola dan menyajikan data sosial ekonomi mikro kependudukan (Regsosek & SDGs Desa) secara transparan, akurat, dan *real-time*. Oleh karena itu, dikembangkan **Sistem Informasi Portal Desa & CMS Berbasis Data Mikro** yang mengintegrasikan pengelolaan data tingkat keluarga dan individu dengan visualisasi grafik interaktif.

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
- **NFR-04 (SEO)**: Implementasi Skema Markup JSON-LD (Organization, WebSite, Breadcrumbs, Article) dan Meta Tag terstruktur.
