# BAB II: ANALISIS KEBUTUHAN (2_REQUIREMENTS_ANALYSIS.md)

---

## 2.1 Kebutuhan Fungsional (Functional Requirements)
- **FR-01 (Manajemen Data Mikro)**: Mengimpor dan mengelola kuesioner keluarga (bangunan, sanitasi, listrik, aset) dan individu warga (pendidikan, pekerjaan, BPJS, PIP, disabilitas) dari berkas Excel.
- **FR-02 (Statistik 2 Arah Dinamis)**: Menyajikan grafik *Horizontal Stacked Bar* dan tabel rincian dengan pembanding dinamis (*Gender*, *Pendidikan*, *Pekerjaan*, *Dusun*, dll).
- **FR-03 (Transparansi APBDes)**: Menyajikan target dan realisasi pendapatan/belanja desa lengkap dengan *progress bar* pencapaian.
- **FR-04 (Layanan Mandiri & Pengaduan)**: Menyediakan permohonan surat online ber-nomor tiket, formulir pengaduan warga, dan buku tamu digital.
- **FR-05 (Ekspor Berkas Resmi)**: Menyediakan ekspor tabel statistik ke format CSV, Excel (XLSX), dan PDF ber-Kop Header Resmi Pemerintah Desa.
- **FR-06 (Manajemen Konten / CMS)**: Mengelola Publikasi Berita (Posts), Kategori, Pengumuman, Galeri, dan Infografis Popup.
- **FR-07 (Informasi Profil Desa)**: Mengelola Data Aparatur Pemerintahan, Lembaga Desa, Potensi Desa, dan Pemetaan Fasilitas Publik (GIS).
- **FR-08 (Keterbukaan Informasi Publik)**: Mengelola repositori Dokumen Publik, Publikasi Desa, dan Data Terbuka (Datasets).
- **FR-09 (Konfigurasi & Sistem)**: Mengelola Pengaturan Website, Metadata, Log Pengunjung, dan Manajemen Pengguna beserta Perannya (RBAC).

---

## 2.2 Kebutuhan Non-Fungsional (Non-Functional Requirements)
- **NFR-01 (Security)**: Proteksi CSRF, sanitasi input XSS, otorisasi peran (Spatie Permission), dan enkripsi password Bcrypt/Argon2.
- **NFR-02 (Performance)**: Optimasi kueri dengan *Eager Loading* untuk mencegah masalah *N+1 Query*, caching statistik pengunjung, dan minifikasi HTML.
- **NFR-03 (Responsiveness)**: Desain antarmuka modern yang responsif di Mobile, Tablet, dan Desktop.
- **NFR-04 (SEO)**: Implementasi Skema Markup JSON-LD (Organization, WebSite, Breadcrumbs, Article) dan Meta Tag terstruktur.
