# BAB VI: API (CHAPTER_6_API.md)

---

## 6.1 Endpoint Statistik Dinamis (`GET /statistik`)
- **Query Parameters**:
  - `kategori` (`string`): Slug kategori (`pekerjaan`, `pendidikan`).
  - `dusun_id` (`integer`): ID Dusun untuk menyaring data per wilayah.
  - `year` (`integer`): Tahun data statistik (`2026`).
  - `json` (`integer`): Set `1` untuk mengembalikan respons JSON asli.

- **Respon JSON Sample**:
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

---

## 6.2 Endpoint Layanan Mandiri & Pengaduan Warga
- `POST /layanan-mandiri/permohonan`: Pengajuan permohonan surat warga (menghasilkan Nomor Tiket SRT-YYYYMMDD-XXXX).
- `POST /pengaduan`: Formulir pengaduan warga online terintegrasi ke dashboard operator.
