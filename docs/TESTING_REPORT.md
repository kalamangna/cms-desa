# TESTING REPORT DOCUMENT (TESTING_REPORT.md)
## BAB IX: PENGUJIAN SISTEM

---

## BAB IX: PENGUJIAN

### 9.1 Hasil Pengujian Otomatis (Automated Testing)
Pengujian suite menggunakan PHPUnit / Pest:
- **Test Case**: `Tests\Feature\StatisticDashboardTest`
- **Hasil**: **PASS (100%)** — 3 Assertions Lulus.

### 9.2 Pengujian Penerimaan Pengguna (User Acceptance Testing)
| No | Fitur Uji | Skenario | Hasil Diharapkan | Status |
|:---|:---|:---|:---|:---:|
| 1 | Impor Data Mikro | Upload Excel 300+ Kolom | Data tersimpan & ter-normalize *Title Case* | SUCCESS |
| 2 | Opsi Pembanding | Centang opsi pembanding di Filament | Dropdown & Grafik Stacked Bar ter-update | SUCCESS |
| 3 | Ekspor Kop Resmi | Klik Ekspor PDF/Excel | File terunduh ber-Kop Resmi Desa | SUCCESS |
| 4 | Sticky Table Cell | Scroll horizontal tabel | Kolom Indikator terkunci tanpa overflow | SUCCESS |
