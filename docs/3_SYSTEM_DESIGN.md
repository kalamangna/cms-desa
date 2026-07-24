# BAB III: PERANCANGAN SISTEM (3_SYSTEM_DESIGN.md)

---

## 3.1 Arsitektur Perangkat Lunak
Sistem dibangun menggunakan pola **Model-View-Controller (MVC)** modern berbasis Laravel 12:
- **Backend Framework**: Laravel 12 (PHP 8.3+)
- **Admin Panel Engine**: Filament v4
- **Frontend Styling**: Tailwind CSS v4 & Alpine.js
- **Visualisasi & Pemetaan**: ApexCharts & Leaflet.js

---

## 3.2 Diagram Alir Data (Data Flow Diagram)
```
[ Warga / Publik ]            [ Operator / Admin ]
        │                            │
        ▼                            ▼
┌──────────────────┐        ┌──────────────────┐
│   Web Frontend   │        │ Filament Admin   │
│ (Portal, Publikasi,       │ (Kelola Data Mikro,
│  Layanan, GIS Map)        │  CMS, Profil, Pengaturan)
└────────┬─────────┘        └────────┬─────────┘
         │                           │
         └─────────────┬─────────────┘
                       ▼
           ┌──────────────────────┐
           │  Laravel Core Engine │
           │  (Routes, Middleware,│
           │   Controller, Service)
           └───────────┬──────────┘
                       ▼
           ┌──────────────────────┐
           │ Database (MySQL/Maria)│
           │ (Penduduk, CMS, Log)  │
           └──────────────────────┘
```
