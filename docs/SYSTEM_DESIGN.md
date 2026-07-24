# SYSTEM DESIGN & IMPLEMENTATION DOCUMENT (SYSTEM_DESIGN.md)
## BAB III: PERANCANGAN SISTEM & BAB IV: IMPLEMENTASI

---

## BAB III: PERANCANGAN SISTEM

### 3.1 Arsitektur Perangkat Lunak
Sistem dibangun menggunakan pola **Model-View-Controller (MVC)** modern berbasis Laravel 12:
- **Backend Framework**: Laravel 12 (PHP 8.3+)
- **Admin Panel Engine**: Filament v4
- **Frontend Styling**: Tailwind CSS v4 & Alpine.js
- **Visualisasi & Pemetaan**: ApexCharts & Leaflet.js

### 3.2 Diagram Alir Data (Data Flow Diagram)
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
- **Laravel 12 & Filament v4**: Pengelolaan data CRUD dan manajemen otorisasi admin.
- **Tailwind CSS v4 & Alpine.js**: Styling komponen antarmuka publik dan manipulasi DOM dinamis.
- **ApexCharts**: Rendering grafik batang tumpuk horizontal (*Horizontal Stacked Bar*) secara responsif.

### 4.2 Fitur Visualisasi Stacked Bar 2 Arah Dinamis
Ketika pembanding 2 arah diaktifkan (misal: *Pekerjaan x Pendidikan*), grafik secara otomatis dikunci dalam mode **Horizontal Stacked Bar Chart** dengan kalkulasi tinggi dinamis (`Math.max(380, count * 48)px`) agar label profesi berdiri tegak lurus dan rapi.

### 4.3 Penguncian Kolom Tabel (Sticky Column)
Kolom `Indikator` pada `<thead>`, `<tbody>`, dan `<tfoot>` diterapkan kelas CSS `sticky left-0` dengan warna background solid 100% (`bg-white` / `bg-slate-50`), *z-index* tinggi (`z-10`/`z-20`), dan bayangan pemisah (`shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]`) agar data tidak bocor saat tabel di-scroll.
