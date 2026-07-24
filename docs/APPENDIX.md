# LAMPIRAN (APPENDIX.md)

---

## 📜 LAMPIRAN A: RIWAYAT PERUBAHAN (CHANGELOG SUMMARY)
- **v1.8.2**: Fitur Multi-Pembanding Dinamis 2 Arah, Horizontal Stacked Bar Chart, Sticky Column Solid, dan Konsolidasi Dokumen.
- **v1.8.1**: Reorganisasi 5 Tab Form Penduduk dan 4 Tab Form Keluarga.
- **v1.8.0**: Multi-Program Bantuan Sosial CheckboxList & Normalisasi Status Pekerjaan Title Case.

---

## 🖼️ LAMPIRAN B: TANGKAPAN LAYAR TAMPILAN (SCREENSHOT)
- *Dashboard Statistik Public* (`/statistik`): Visualisasi grafik Horizontal Stacked Bar Chart dan Tabel 2 Arah.
- *Filament Admin Panel* (`/admin`): Form Input Penduduk (5 Tab), Form Keluarga (4 Tab), & CheckboxList `secondary_columns`.
- *Halaman Transparansi APBDes* (`/apbdes`): Progress bar realisasi pendapatan & belanja desa.

---

## 🗄️ LAMPIRAN C: DIAGRAM RELASI BASIS DATA (ERD)
Diagram relasi entitas kependudukan: `dusuns` (1:N) $\rightarrow$ `families` (1:N) $\rightarrow$ `citizens`.

---

## 📁 LAMPIRAN D: STRUKTUR FOLDER PROYEK (DIRECTORY TREE)
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
│   ├── COVER_AND_PREFACE.md
│   ├── CHAPTER_1_INTRODUCTION.md
│   ├── CHAPTER_2_REQUIREMENTS_ANALYSIS.md
│   ├── CHAPTER_3_SYSTEM_DESIGN.md
│   ├── CHAPTER_4_IMPLEMENTATION.md
│   ├── CHAPTER_5_DATABASE.md
│   ├── CHAPTER_6_API.md
│   ├── CHAPTER_7_INSTALLATION_GUIDE.md
│   ├── CHAPTER_8_USER_GUIDE.md
│   ├── CHAPTER_9_TESTING.md
│   ├── CHAPTER_10_MAINTENANCE.md
│   ├── APPENDIX.md
│   ├── TECHNICAL_REPORT.md
│   └── CHANGELOG.md
├── resources/views/
│   └── statistics/index.blade.php
├── routes/web.php
└── README.md
```
