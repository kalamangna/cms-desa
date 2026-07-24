# DOKUMENTASI API & ENDPOINT (API.md)

Dokumen ini mencatat rincian endpoint API internal dan publik yang disediakan oleh **Portal Resmi Website & CMS Desa**.

---

## 🌐 1. Endpoint Statistik Publik (`/statistik`)

### `GET /statistik`
Mengembalikan data kependudukan dan visualisasi statistik. Endpoint ini mendukung parameter kueri AJAX untuk pengisian data grafik/tabel tanpa reload halaman.

#### Parameter Kueri (Query Parameters):
| Parameter | Tipe | Deskripsi | Contoh |
|:---|:---|:---|:---|
| `kategori` | `string` | Slug kategori statistik yang dipilih | `pekerjaan`, `pendidikan` |
| `dusun_id` | `integer` | ID Dusun untuk menyaring data per wilayah | `1`, `2` |
| `year` | `integer` | Tahun data statistik | `2026` |
| `json` | `integer` | Set `1` untuk mengembalikan respons JSON asli | `1` |

#### Contoh Respon JSON (`GET /statistik?kategori=pekerjaan&json=1`):
```json
{
  "pekerjaan": {
    "name": "Pekerjaan",
    "isCitizens": true,
    "secondaryConfigs": {
      "gender": {
        "label": "Jenis Kelamin",
        "options": ["Laki-laki", "Perempuan"],
        "colors": ["#0ea5e9", "#ec4899"]
      },
      "education_level": {
        "label": "Pendidikan",
        "options": ["SD / Sederajat", "SMP / Sederajat", "SMA / Sederajat", "S1 / D4"],
        "colors": ["#10b981", "#3b82f6", "#f59e0b", "#8b5cf6"]
      }
    },
    "years": [2024, 2025, 2026],
    "indicators": [
      {
        "name": "Petani / Pekebun",
        "unit": "Jiwa",
        "breakdowns": {
          "gender": { "Laki-laki": 450, "Perempuan": 210 },
          "education_level": { "SD / Sederajat": 300, "SMP / Sederajat": 250, "SMA / Sederajat": 110 }
        },
        "data": [
          { "year": 2026, "value": 660, "value_male": 450, "value_female": 210 }
        ]
      }
    ]
  }
}
```

---

## 📑 2. Endpoint Layanan Mandiri & Pengaduan Warga

### `POST /layanan-mandiri/permohonan`
Mengirimkan formulir permohonan surat administrasi warga.

#### Body Payload (Form Data):
```json
{
  "citizen_nik": "3515012304900001",
  "letter_type": "Surat Keterangan Usaha",
  "purpose": "Persyaratan Pengajuan KUR Bank",
  "phone": "081234567890"
}
```
#### Respon Sukses (200 OK):
```json
{
  "success": true,
  "ticket_number": "SRT-20260724-0012",
  "message": "Permohonan surat berhasil dikirim. Simpan nomor tiket Anda untuk melacak status."
}
```

---

### `POST /pengaduan`
Mengirimkan laporan aduan warga online.

#### Body Payload (Form Data):
```json
{
  "name": "Budi Santoso",
  "phone": "081299887766",
  "category": "Infrastruktur",
  "title": "Jalan Berlubang di Dusun Krajan",
  "content": "Mohon perbaikan jalan berlubang di RT 02 RW 01."
}
```

---

## 📊 3. Endpoint Tracking Pengunjung (`visitor_logs`)

### Middleware Pelacak Pengunjung
Setiap permintaan ke halaman publik secara otomatis mencatat data analitik ke database via `VisitorLogMiddleware`:
- Alamat IP Visitor (dikombinasikan dengan Hash Enkripsi untuk Privasi).
- User Agent (Browser & Sistem Operasi).
- Halaman yang dikunjungi (*URL Path*).
- Timestamp Kunjungan.
