# STRUKTUR DATABASE & STRUKTUR DATA (DATABASE.md)

Dokumen ini menjelaskan struktur arsitektur basis data, tabel, relasi entitas (ERD), dan tipe data yang digunakan pada **Portal Resmi Website & CMS Desa**.

---

## 🏛️ 1. Diagram Relasi Entitas Utama (ERD Overview)

```
       ┌──────────────┐
       │    dusuns    │
       └──────┬───────┘
              │ (1:N)
              ▼
       ┌──────────────┐
       │   families   │
       └──────┬───────┘
              │ (1:N)
              ▼
       ┌──────────────┐
       │   citizens   │
       └──────────────┘

┌──────────────────────┐        ┌──────────────────────┐
│ statistic_categories │ ──(1:N)─►│ statistic_indicators │
└──────────────────────┘        └──────────────────────┘
```

---

## 📋 2. Rincian Tabel Utama Database

### 2.1 Tabel `dusuns` (Wilayah Administrasi)
| Kolom | Tipe Data | Keterangan |
|:---|:---|:---|
| `id` | `BIGINT UNSIGNED (PK)` | Primary Key Auto Increment |
| `name` | `VARCHAR(255)` | Nama Dusun |
| `head_name` | `VARCHAR(255)` | Nama Kepala Dusun |
| `created_at` / `updated_at` | `TIMESTAMP` | Waktu Pembuatan & Perubahan |

---

### 2.2 Tabel `families` (Data Mikro Keluarga)
| Kolom | Tipe Data | Keterangan |
|:---|:---|:---|
| `id` | `BIGINT UNSIGNED (PK)` | Primary Key Auto Increment |
| `dusun_id` | `BIGINT UNSIGNED (FK)` | Relasi ke `dusuns.id` |
| `family_card_number` | `VARCHAR(16)` | Nomor Kartu Keluarga (KK) |
| `head_of_family_name` | `VARCHAR(255)` | Nama Kepala Keluarga |
| `address` | `TEXT` | Alamat Rumah |
| `rt` / `rw` | `VARCHAR(10)` | Nomor RT dan RW |
| `building_type` | `VARCHAR(255)` | Jenis Bangunan Rumah |
| `ownership_status` | `VARCHAR(255)` | Status Kepemilikan Rumah |
| `floor_material` | `VARCHAR(255)` | Bahan Material Lantai |
| `wall_material` | `VARCHAR(255)` | Bahan Material Dinding |
| `roof_material` | `VARCHAR(255)` | Bahan Material Atap |
| `electricity_power_meter_1` | `VARCHAR(100)` | Daya Meteran Listrik 1 |
| `electricity_power_meter_2` | `VARCHAR(100)` | Daya Meteran Listrik 2 |
| `electricity_power_meter_3` | `VARCHAR(100)` | Daya Meteran Listrik 3 |
| `water_source` | `VARCHAR(255)` | Sumber Air Minum Utama |
| `assistance_type` | `TEXT (JSON/CSV)` | Program Bantuan Sosial (PKH, BPNT, BLT, dll) |
| `motorcycle_value` | `BIGINT` | Nilai Aset Sepeda Motor |
| `car_value` | `BIGINT` | Nilai Aset Mobil |

---

### 2.3 Tabel `citizens` (Data Mikro Penduduk)
| Kolom | Tipe Data | Keterangan |
|:---|:---|:---|
| `id` | `BIGINT UNSIGNED (PK)` | Primary Key Auto Increment |
| `family_id` | `BIGINT UNSIGNED (FK)` | Relasi ke `families.id` |
| `dusun_id` | `BIGINT UNSIGNED (FK)` | Relasi ke `dusuns.id` |
| `nik` | `VARCHAR(16)` | Nomor Induk Kependudukan (NIK) |
| `name` | `VARCHAR(255)` | Nama Lengkap Penduduk (UPPERCASE) |
| `gender` | `ENUM('Laki-laki', 'Perempuan')` | Jenis Kelamin |
| `family_relationship` | `VARCHAR(100)` | Hubungan Dalam Keluarga |
| `education_level` | `VARCHAR(255)` | Tingkat Pendidikan Terakhir |
| `job` | `VARCHAR(255)` | Bidang Profesi Pekerjaan Utama |
| `job_status` | `VARCHAR(255)` | Status Pekerjaan Baku |
| `marital_status` | `VARCHAR(100)` | Status Perkawinan |
| `bpjs_status` | `VARCHAR(100)` | Kepesertaan BPJS Kesehatan |
| `pip_status` | `VARCHAR(100)` | Penerima Program Indonesia Pintar (PIP) |
| `has_digital_wallet` | `VARCHAR(255)` | Status Rekening / Dompet Digital |
| `domicile_address_type` | `VARCHAR(255)` | Kesesuaian Alamat Domisili |

---

### 2.4 Tabel `statistic_categories` (Kategori Statistik Dinamis)
| Kolom | Tipe Data | Keterangan |
|:---|:---|:---|
| `id` | `BIGINT UNSIGNED (PK)` | Primary Key Auto Increment |
| `name` | `VARCHAR(255)` | Nama Kategori Statistik |
| `slug` | `VARCHAR(255)` | Slug URL Kategori |
| `mapping_table` | `VARCHAR(100)` | Tabel Sumber Data (`citizens` / `families`) |
| `secondary_columns` | `JSON` | Array Kolom Pembanding Sumbu Ke-2 (misal: `["gender", "education_level"]`) |
| `is_active` | `BOOLEAN` | Tampilkan di Halaman Publik |

---

### 2.5 Tabel `budget_realizations` (Transparansi APBDes)
| Kolom | Tipe Data | Keterangan |
|:---|:---|:---|
| `id` | `BIGINT UNSIGNED (PK)` | Primary Key Auto Increment |
| `year` | `YEAR` | Tahun Anggaran |
| `type` | `ENUM('income', 'expense', 'financing')` | Jenis Alokasi Anggaran |
| `category_name` | `VARCHAR(255)` | Nama Sub-Kategori Anggaran |
| `budget_amount` | `BIGINT` | Target Anggaran |
| `realization_amount` | `BIGINT` | Realisasi Anggaran |

---

## 🔒 3. Indeks & Optimasi Kinerja Database

- **Indeks Unik**: `citizens.nik` (Unique), `families.family_card_number` (Indexed).
- **Indeks Foreign Key**: `citizens.family_id`, `citizens.dusun_id`, `families.dusun_id`.
- **Eager Loading Optimization**: Seluruh pengambilan data kependudukan publik menggunakan kueri `with(['dusun', 'family'])` untuk menjamin performa tinggi tanpa masalah *N+1 Query*.
