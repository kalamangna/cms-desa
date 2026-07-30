# 🎨 Panduan Universal Sistem Desain (Design System)

Dokumen ini menjadi acuan utama dalam merancang antarmuka pengguna (UI) agar konsisten, intuitif, responsif, dan mudah dipelihara pada berbagai skala dan jenis proyek perangkat lunak.

## 1. Filosofi & Prinsip Utama
- **Fungsi di Atas Dekorasi:** Utamakan pengalaman pengguna (sederhana, konsisten, responsif, cepat, aksesibel). Estetika harus mendukung fungsi, bukan mengaburkannya.
- **Mobile First:** Mulai rancangan dari pemikiran layar kecil (*mobile*), lalu perluas dan kembangkan untuk perangkat besar (*tablet*, *desktop*).
- **Konsistensi Visual:** Gunakan komponen UI yang identik di seluruh bagian aplikasi. Jangan buat variasi turunan baru jika komponen dengan fungsi serupa sudah ada.
- **Hierarki Visual:** Gunakan skala ukuran teks, bobot *font*, kontras warna, dan ruang kosong (*whitespace*) untuk memandu fokus mata pengguna secara naluriah.
- **Simplicity & Performance:** Hilangkan pernak-pernik visual tanpa fungsi operasional. Optimalkan aset, hindari skrip JS berlebih, dan terapkan pemuatan tunda (*lazy loading*).
- **Prinsip Akhir:** Desain terbaik adalah desain yang "transparan" di mata pengguna. Fokus antarmuka adalah membantu selesainya tugas tanpa paksaan berpikir (*don't make me think*).

## 2. Design Token & Pewarnaan
- **Warna Utama (Theme Color):** Gunakan variabel tema (contoh: *CSS Variable* `var(--color-primary)` atau utilitas `bg-primary`) alih-alih menyuntikkan nilai *hex/RGB* kaku (*hardcoded*). Ini memudahkan fitur penggantian tema secara masif.
- **Warna Netral (Neutral Color):** Tetapkan skala abu-abu statis yang konsisten untuk digunakan pada teks, latar belakang, batas, dan pembagi.
- **Warna Semantik:** Patuhi konvensi warna universal untuk umpan balik interaksi (*Success* = Hijau, *Warning* = Kuning/Jingga, *Danger* = Merah, *Info* = Biru).
- **Dark Mode:** Ekosistem desain harus secara fundamental mendukung mode gelap melalui pemanggilan variabel terpusat, bukan pewarnaan statis.

## 3. Tipografi
- Gunakan tak lebih dari dua varian keluarga *font* mumpuni (diutamakan jenis *sans-serif* untuk web).
- Terapkan hierarki ketat berdasarkan perhitungan skala (*Display, Heading, Subheading, Body, Caption*).
- Atur tinggi antar baris (*line-height*) secara harmonis dan batasi batas maksimal karakter per baris paragraf agar mata tidak letih saat membaca.

## 4. Sistem Layout & Spasial
- **Layout Responsif:** Layar harus elastis beradaptasi dengan batas tepi perangkat. Dilarang keras menimbulkan *horizontal scroll*, susunan tertumpuk acak, teks terpotong, atau tombol merapat.
- **Spacing Scale:** Terapkan skala jarak/margin baku yang berpedoman pada sistem matematika tetap (contoh: kelipatan 4px atau 8px HANYA). Hindari penetapan spasi angka acak.
- **Whitespace:** Biarkan ruang kosong mengambil alih sela antar blok elemen. Area yang saling berhimpit menciptakan kepadatan (*clutter*) yang fatal.

## 5. Visual Foundation
- **Garis Tepi (Border) & Radius:** Garis pembatas ditujukan murni sebagai pemilah wilayah dengan warna redup transparan. Standarisasikan rasio kelengkungan (*border-radius*) secara global.
- **Bayangan (Shadow) & Elevasi:** Gunakan bayangan sangat halus (*subtle shadow*) untuk menandakan lapisan depan dan lapisan belakang layar (seperti kartu atau modal *pop-up*).
- **Ikonografi:** Loyal pada satu jenis pustaka ikon (*icon pack*). Bobot guratan (*stroke*) dan dimensinya harus sinkron dengan tipografi penyertanya.
- **Animasi (*Motion*):** Transisi dihalalkan semata-mata untuk menandai perpindahan *state* operasional, BUKAN demi unjuk efek meriah.

## 6. Komponen Interaktif (UI)
- **Tombol (Button):** Wajib menaati struktur 6 kondisi mutlak: *Default, Hover, Focus, Active, Disabled, Loading*.
- **Formulir (Form):** Tidak boleh ada kolom masuk yang buta; sertakan label, petunjuk (*placeholder*), dan responsivitas saat *Focus, Error, dan Disabled*.
- **Kartu (Card):** Efek reaktif peninggi/animasi *(Hover effect)* **HANYA** ditoleransi apabila seluruh kotak tersebut berfungsi sebagai tautan atau tombol yang bisa diklik.
- **Umpan Balik (Feedback):** Menggunakan instrumen universal (*Alert, Toast, Modal*) dengan indikasi sukses/gagal yang lugas.
- **State Kosong & Pemuatan:** Halaman muat memakai *Skeleton Loader* atau pemutar (*Spinner*). Tampilan daftar kosong tak boleh hampa melainkan memajang *Empty State* (berisi grafis ringan + pesan).

## 7. Aksesibilitas Mutlak (a11y)
Setiap atom desain harus bersahabat dengan kaidah WCAG:
- **Dukungan Keyboard:** Ramah tabulasi (tombol *Tab*, *Enter*, *Escape*).
- **Fokus Visual:** Cincin penanda *Focus Ring* yang amat kontras harus muncul ketika elemen aktif.
- **Titik Sentuh (*Touch Target*):** Seluruh elemen yang bisa ditekan oleh layar ponsel harus memiliki pijakan minimal seluas *44x44 pixel* agar ramah jemari.
