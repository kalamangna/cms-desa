# ARSITEKTUR SISTEM & KEAMANAN (SYSTEM_ARCHITECTURE.md)

Dokumen ini menjelaskan arsitektur basis data, rincian API endpoint, serta kebijakan keamanan yang diterapkan pada **Portal Resmi Website & CMS Desa**.

---

## 🗄️ BAB I: PERANCANGAN BASIS DATA & ERD

### 1.1 Diagram Relasi Entitas Utama (ERD Overview)

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

### 1.2 Rincian Tabel Utama Basis Data

#### A. Tabel `dusuns` (Wilayah Administrasi)
- `id` (`BIGINT UNSIGNED PK`): Primary Key Auto Increment.
- `name` (`VARCHAR(255)`): Nama Dusun.
- `head_name` (`VARCHAR(255)`): Nama Kepala Dusun.

#### B. Tabel `families` (Data Mikro Keluarga)
- `id` (`BIGINT UNSIGNED PK`): Primary Key Auto Increment.
- `dusun_id` (`BIGINT UNSIGNED FK`): Relasi ke `dusuns.id`.
- `family_card_number` (`VARCHAR(16)`): Nomor Kartu Keluarga (KK).
- `head_of_family_name` (`VARCHAR(255)`): Nama Kepala Keluarga.
- `address` (`TEXT`): Alamat Rumah.
- `rt` / `rw` (`VARCHAR(10)`): Nomor RT dan RW.
- `building_type`, `ownership_status`, `floor_material`, `wall_material`, `roof_material`: Karakteristik Bangunan Rumah (*Title Case* baku).
- `electricity_power_meter_1..3`: 3 Kolom Meteran Listrik Terpisah.
- `water_source`: Sumber Air Minum Utama.
- `assistance_type`: Program Bantuan Sosial (PKH, BPNT, BLT, dll).
- `motorcycle_value`, `car_value`: Nilai Aset Kendaraan (`BIGINT`).

#### C. Tabel `citizens` (Data Mikro Penduduk)
- `id` (`BIGINT UNSIGNED PK`): Primary Key Auto Increment.
- `family_id` / `dusun_id` (`FK`): Relasi ke `families.id` dan `dusuns.id`.
- `nik` (`VARCHAR(16)`): Nomor Induk Kependudukan (NIK Unique).
- `name` (`VARCHAR(255)`): Nama Lengkap Penduduk (UPPERCASE).
- `gender` (`ENUM('Laki-laki', 'Perempuan')`): Jenis Kelamin.
- `education_level`, `job`, `job_status`, `marital_status`: Profil Sosio-Ekonomi (*Title Case* baku).
- `bpjs_status`, `pip_status`, `has_digital_wallet`: Data Program Jaminan Sosial & Keuangan.
- `domicile_address_type`: Kesesuaian Alamat Domisili.

#### D. Tabel `statistic_categories` (Kategori Statistik Dinamis)
- `name`, `slug`: Identitas Kategori.
- `mapping_table`: Tabel Sumber Data (`citizens` / `families`).
- `secondary_columns` (`JSON`): Array Kolom Pembanding Sumbu Ke-2 (misal: `["gender", "education_level"]`).

---

## 🌐 BAB II: DOKUMENTASI API & ENDPOINT

### 2.1 Endpoint Statistik Publik (`GET /statistik`)
Mengembalikan data kependudukan dan visualisasi statistik. mendukung parameter kueri AJAX untuk pengisian data grafik/tabel tanpa reload halaman.

- **Query Parameters**:
  - `kategori` (`string`): Slug kategori (`pekerjaan`, `pendidikan`).
  - `dusun_id` (`integer`): ID Dusun untuk menyaring data per wilayah.
  - `year` (`integer`): Tahun data statistik (`2026`).
  - `json` (`integer`): Set `1` untuk mengembalikan respons JSON asli.

- **Contoh Respon JSON**:
```json
{
  "pekerjaan": {
    "name": "Pekerjaan",
    "isCitizens": true,
    "secondaryConfigs": {
      "gender": { "label": "Jenis Kelamin", "options": ["Laki-laki", "Perempuan"], "colors": ["#0ea5e9", "#ec4899"] }
    },
    "years": [2026],
    "indicators": [
      {
        "name": "Petani / Pekebun",
        "unit": "Jiwa",
        "breakdowns": { "gender": { "Laki-laki": 450, "Perempuan": 210 } },
        "data": [{ "year": 2026, "value": 660 }]
      }
    ]
  }
}
```

### 2.2 Endpoint Layanan Mandiri & Pengaduan Warga
- `POST /layanan-mandiri/permohonan`: Pengajuan permohonan surat warga (menghasilkan Nomor Tiket).
- `POST /pengaduan`: Formulir pengaduan online warga terintegrasi ke dashboard operator.

---

## 🛡️ BAB III: HARDENING KEAMANAN SISTEM

### 3.1 Perlindungan Form & Input
- **CSRF Protection**: Seluruh formulir diproteksi oleh middleware CSRF token.
- **Sanitasi Input & XSS**: String HTML pada komponen Alpine.js di-escape secara presisi.
- **PDO Binding**: Operasi basis data menggunakan Prepared Statements untuk mencegah SQL Injection.

### 3.2 Autentikasi & Keamanan File
- **RBAC**: Otorisasi berbasis peran menggunakan `Spatie/Laravel-Permission`.
- **Password Hashing**: Kata sandi dienkripsi dengan algoritma **Bcrypt / Argon2id**.
- **File Upload Security**: Validasi ketat ekstensi berkas gambar (`.jpg`, `.png`, `.webp`, `.pdf`) dan penolakan berkas skrip berisiko (`.php`, `.exe`, `.sh`).
- **Global HTTPS**: Seluruh tautan media dipaksa menggunakan skema `https://` pada lingkungan produksi.
