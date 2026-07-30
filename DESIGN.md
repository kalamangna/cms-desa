# 🎨 Design System CMS Desa

Dokumen ini menjadi acuan dalam merancang antarmuka CMS Desa agar konsisten, mudah digunakan, responsif, dan mudah dipelihara.

## 1. Filosofi & Prinsip Utama
- **Fungsi di Atas Dekorasi:** Utamakan pengalaman pengguna (sederhana, konsisten, responsif, cepat, aksesibel).
- **Mobile First:** Mulai rancangan dari ukuran layar kecil (*mobile*), lalu kembangkan untuk *tablet* dan *desktop*.
- **Konsistensi:** Gunakan komponen yang sama di seluruh aplikasi. Jangan membuat variasi baru jika komponen serupa sudah ada.
- **Hierarki Visual:** Gunakan ukuran, bobot *font*, warna, dan *whitespace* untuk mengarahkan fokus pengguna.
- **Simplicity & Performance:** Hilangkan elemen visual tanpa fungsi. Optimalkan gambar, kurangi animasi/JS yang tidak perlu, dan gunakan *lazy loading*.
- **Prinsip Akhir:** Desain yang baik adalah desain yang hampir tidak disadari pengguna. Fokus pada penyelesaian tugas dengan cepat.

## 2. Design Token & Pewarnaan
- **Theme Color (Dinamis):** Gunakan kelas utilitas tema yang terhubung ke CSS Variable (contoh: `bg-primary`, `text-primary`, `--primary-hover`). ❌ Jangan *hardcode* warna Tailwind utama (seperti `bg-emerald-600`) agar aplikasi bisa mengikuti pergantian warna tema dari admin.
- **Neutral Color (Statis):** Gunakan skala warna statis yang konsisten (seperti `slate-50` hingga `slate-900`). Warna netral tidak boleh terpengaruh tema.
- **Semantic Color:** Gunakan warna statis khusus dan universal untuk umpan balik status (*Success, Warning, Danger, Info*).
- **Dark Mode:** Seluruh komponen harus mendukung mode gelap melalui integrasi *Theme Token*, jangan pewarnaan gelap kaku.

## 3. Tipografi
- Gunakan maksimal satu keluarga *font* modern yang mudah dibaca.
- Jaga konsistensi hierarki ukuran (*Display, Heading, Subheading, Body, Caption*).
- Gunakan *line-height* yang nyaman dan hindari paragraf yang membentang terlalu lebar.

## 4. Sistem Layout & Spasial
- **Layout & Grid responsif:** Konten harus beradaptasi terhadap layar. Dilarang ada *horizontal scroll*, elemen bertumpuk berantakan, teks terpotong, atau tombol kekecilan.
- **Spacing Scale:** Gunakan skala *spacing* Tailwind yang baku (misal 4, 8, 12, 16, 24, 32, 48, 64). Dilarang menebak angka jarak secara acak.
- **Whitespace:** Berikan ruang bernapas antar elemen. Jangan berdesak-desakan.

## 5. Visual Foundation
- **Border & Radius:** Gunakan garis tepi (*border*) tipis berwarna netral murni sebagai pemisah. Konsisten menggunakan batas lengkungan (*border-radius*).
- **Shadow & Surface:** Gunakan *shadow* ringan untuk membedakan kedalaman (elevasi). Hindari bayangan pekat yang tebal.
- **Icon:** Seragam menggunakan 1 *library* ikon. Ukuran mengikuti teks, berfungsi sebagai pendukung informasi, bukan fokus utama.
- **Animasi:** Gunakan animasi berdurasi singkat hanya untuk membantu pemahaman antarmuka, dilarang dipakai sekadar untuk dekorasi meriah.

## 6. Komponen Interaktif (UI)
- **Tombol (Button):** Wajib menggunakan struktur konsisten dengan 6 *state*: *Default, Hover, Focus, Active, Disabled, Loading*.
- **Formulir (Form):** Wajib terstruktur (Label, Input, Placeholder) dan memiliki 3 indikator visual: *Focus, Error, Disabled*.
- **Card:** Terdiri atas *Container* dan *Content* (serta opsional *Header/Footer*). Efek *hover* HANYA diberikan jika *card* bisa diklik.
- **Feedback:** Wujud standar (Alert, Toast, Banner, Dialog) dengan pesan/status yang langsung bisa ditebak maknanya.
- **Loading & Empty State:** Utamakan *Skeleton Loading* (spinner hanya untuk proses kilat). Layar kosong harus selalu diisi *Empty State* (ilustrasi, judul, deksripsi).

## 7. Aksesibilitas (a11y)
Seluruh komponen harus:
- Bisa digunakan/dinavigasi hanya menggunakan sistem *keyboard*.
- Memiliki *focus indicator* yang terlihat jelas saat elemen aktif.
- Kontras warna memadai antara teks dan latar belakang.
- Memiliki bidang klik (area sentuh) yang cukup lebar.
