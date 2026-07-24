# DATABASE DESIGN DOCUMENT (DATABASE.md)
## BAB V: DATABASE & LAMPIRAN C: ERD

---

## BAB V: DATABASE

### 5.1 ERD & Relasi Entitas Utama
Relasi antar tabel kependudukan: `dusuns` (1:N) $\rightarrow$ `families` (1:N) $\rightarrow$ `citizens`.

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

### 5.2 Spesifikasi Tabel Utama
- **`dusuns`**: `id` (PK), `name`, `head_name`.
- **`families`**: `id` (PK), `dusun_id` (FK), `family_card_number`, `head_of_family_name`, `address`, `building_type`, `ownership_status`, `floor_material`, `wall_material`, `roof_material`, `electricity_power_meter_1..3`, `water_source`, `assistance_type`, `motorcycle_value`, `car_value`.
- **`citizens`**: `id` (PK), `family_id` (FK), `dusun_id` (FK), `nik` (Unique), `name` (UPPERCASE), `gender`, `family_relationship`, `education_level`, `job`, `job_status`, `marital_status`, `bpjs_status`, `pip_status`, `has_digital_wallet`, `domicile_address_type`.
- **`statistic_categories`**: `id` (PK), `name`, `slug`, `mapping_table`, `secondary_columns` (JSON).
- **`budget_realizations`**: `id` (PK), `year`, `type`, `category_name`, `budget_amount`, `realization_amount`.
