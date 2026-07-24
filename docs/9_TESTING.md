# BAB IX: PENGUJIAN (CHAPTER_9_TESTING.md)

---

## 9.1 Hasil Pengujian Otomatis (Automated Testing)
Pengujian backend otomatis dieksekusi menggunakan **PHPUnit / Laravel Pest Framework** dengan lingkungan database terisolasi (`RefreshDatabase`).
- **Status Akhir**: **100% PASS** — 40 Test Suites, 72 Assertions Lulus.

---

## 9.2 Pengujian Penerimaan Pengguna (User Acceptance Testing / UAT)
| No | Fitur Uji | Skenario | Hasil Diharapkan | Status |
|:---|:---|:---|:---|:---:|
| 1 | Impor Data Mikro | Upload Excel 300+ Kolom | Data tersimpan & ter-normalize *Title Case* | SUCCESS |
| 2 | Opsi Pembanding | Centang opsi pembanding di Filament | Dropdown & Grafik Stacked Bar ter-update | SUCCESS |
| 3 | Ekspor Kop Resmi | Klik Ekspor PDF/Excel | File terunduh ber-Kop Resmi Desa | SUCCESS |
| 4 | Sticky Table Cell | Scroll horizontal tabel | Kolom Indikator terkunci tanpa overflow | SUCCESS |
| 5 | WhatsApp Preview | Upload foto berita kamera HP | File ter-kompresi < 300 KB & link HTTPS | SUCCESS |
| 6 | Layanan Surat | Input NIK & Jenis Surat | Mendapatkan Nomor Tiket SRT-YYYYMMDD-XXXX | SUCCESS |
