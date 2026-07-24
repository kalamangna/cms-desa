# BAB IV: IMPLEMENTASI (CHAPTER_4_IMPLEMENTATION.md)

---

## 4.1 Teknologi & Pustaka Pendukung
Sistem mengintegrasikan berbagai teknologi mutakhir:
- **Laravel 12 & Filament v4**: Pengelolaan data CRUD dan manajemen otorisasi admin.
- **Tailwind CSS v4 & Alpine.js**: Styling komponen antarmuka publik dan manipulasi DOM dinamis.
- **ApexCharts**: Rendering grafik batang tumpuk horizontal (*Horizontal Stacked Bar*) secara responsif.

---

## 4.2 Fitur Visualisasi Stacked Bar 2 Arah Dinamis
Ketika pembanding 2 arah diaktifkan (misal: *Pekerjaan x Pendidikan*), grafik secara otomatis dikunci dalam mode **Horizontal Stacked Bar Chart** dengan kalkulasi tinggi dinamis (`Math.max(380, count * 48)px`) agar label profesi berdiri tegak lurus dan rapi.

---

## 4.3 Penguncian Kolom Tabel (Sticky Column)
Kolom `Indikator` pada `<thead>`, `<tbody>`, dan `<tfoot>` diterapkan kelas CSS `sticky left-0` dengan warna background solid 100% (`bg-white` / `bg-slate-50`), *z-index* tinggi (`z-10`/`z-20`), dan bayangan pemisah (`shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]`) agar data tidak bocor saat tabel di-scroll.
