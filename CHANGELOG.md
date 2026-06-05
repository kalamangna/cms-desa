# Changelog - Website Desa Cantik

Semua perubahan signifikan pada proyek ini akan didokumentasikan di file ini.

## [1.0.0] - 2024-06-04

### Added
- **Arsitektur Utama**: Inisialisasi Laravel 11 dengan Filament Admin 4.
- **Frontend Modern**: Desain portal berbasis Tailwind CSS 4 dengan sistem desain Emerald & Sky yang elegan.
- **Homepage Dinamis**:
    - Hero section dengan tagline "Desa Cantik" dan visualisasi data utama.
    - Statistik Utama: Penduduk, Dusun, UMKM, dan Luas Wilayah (terintegrasi database).
    - Dashboard Preview: Grafik distribusi pekerjaan dan pendidikan (Chart.js).
    - Transparansi APBDes: Kartu ringkasan anggaran dan grafik alokasi belanja.
    - Peta Interaktif: Integrasi Leaflet.js dengan koordinat dinamis dari pengaturan desa.
    - Galeri Kegiatan: Layout Masonry yang dinamis.
- **Halaman Pemerintahan**: Profil pejabat desa dengan desain kartu modern.
- **Portal Berita**: Layout "1 Featured + 6 Recent" dengan fitur kategori dan pencarian.
- **Dashboard Statistik**: Halaman pusat data lengkap dengan visualisasi tren kependudukan dan sosial.
- **Open Data & Publikasi**: Katalog dataset dan dokumen digital (PDF) yang siap unduh.
- **Backend Admin**: Resource Filament lengkap untuk mengelola semua konten frontend.

### Changed
- **Ikonografi Global**: Migrasi dari Heroicons ke FontAwesome 6 Pro untuk tampilan yang lebih premium dan variatif.
- **SEO & Social Sharing**: Meta tags dinamis pada layout utama untuk optimasi pratinjau link di media sosial.
- **Paginasi**: Kustomisasi tampilan paginasi Laravel agar selaras dengan desain Tailwind UI aplikasi.

### Optimized
- **Performa**: Implementasi mechanism *Query Caching* pada HomeController untuk meningkatkan kecepatan akses halaman depan.
- **Dinamisasi Konten**: Seluruh teks sambutan, judul, dan koordinat peta kini ditarik dari tabel `Settings`.
- **Database**: Migrasi konfigurasi dari SQLite ke MySQL untuk kesiapan deployment produksi.

---
*Dikembangkan dengan ❤️ untuk Program Desa Cantik BPS.*
