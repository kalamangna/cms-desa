# BAB I: PENDAHULUAN (1_INTRODUCTION.md)

---

## 1.1 Latar Belakang
Pemerintah Desa memerlukan portal informasi digital yang tidak hanya berfungsi sebagai media publikasi berita dan pengumuman, melainkan juga mampu mengelola dan menyajikan data sosial ekonomi mikro kependudukan (Regsosek & SDGs Desa) secara transparan, akurat, dan *real-time*. Oleh karena itu, dikembangkan **Sistem Informasi Portal Desa & CMS Berbasis Data Mikro** yang mengintegrasikan pengelolaan data tingkat keluarga dan individu dengan visualisasi grafik interaktif.

---

## 1.2 Maksud dan Tujuan
1. Menyediakan portal publik desa yang modern, responsif, dan mudah diakses oleh masyarakat sebagai pusat informasi terpadu (Berita, Pengumuman, Profil Pemerintahan, dan GIS Fasilitas Publik).
2. Mengelola data mikro kependudukan terstruktur (`Dusun` $\rightarrow$ `Keluarga` $\rightarrow$ `Penduduk`) via Admin Panel terintegrasi.
3. Menyajikan visualisasi data statistik sektoral 1 arah dan 2 arah (*cross-tabulation* dinamis) yang didukung ekspor data resmi (CSV, Excel, PDF).
4. Menyediakan layanan mandiri permohonan surat, pengaduan warga, serta transparansi realisasi APBDes dan penyediaan repositori data terbuka (Dataset).

---

## 1.3 Ruang Lingkup
Ruang lingkup proyek mencakup pembangunan *backend* Laravel 12, *admin panel* Filament v4 (untuk manajemen data mikro, CMS, profil, layanan, dan konfigurasi global), antarmuka publik berbasis Tailwind CSS v4, visualisasi grafik ApexCharts, pemetaan wilayah Leaflet.js, serta mekanisme pengamanan (*RBAC*) dan optimasi penelusuran (*SEO*).
