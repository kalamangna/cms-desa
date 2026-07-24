# BAB V: DATABASE (5_DATABASE.md)

---

## 5.1 ERD & Relasi Entitas Utama
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

---

## 5.2 Spesifikasi Tabel Utama
- **`dusuns`**: `id` (PK), `name`, `head_name`.
- **`families`**: `id` (PK), `dusun_id` (FK), `family_card_number`, `head_of_family_name`, `address`, `building_type`, `ownership_status`, `floor_material`, `wall_material`, `roof_material`, `electricity_power_meter_1..3`, `water_source`, `assistance_type`, `motorcycle_value`, `car_value`.
- **`citizens`**: `id` (PK), `family_id` (FK), `dusun_id` (FK), `nik` (Unique), `name` (UPPERCASE), `gender`, `family_relationship`, `education_level`, `job`, `job_status`, `marital_status`, `bpjs_status`, `pip_status`, `has_digital_wallet`, `domicile_address_type`.
- **`statistic_categories`**: `id` (PK), `name`, `slug`, `mapping_table`, `secondary_columns` (JSON).
- **`statistic_indicators`**: `id` (PK), `category_id` (FK), `name`, `mapping_column`, `order`.
- **`statistic_data`**: `id` (PK), `indicator_id` (FK), `year`, `value`.
- **`budget_categories`**: `id` (PK), `name`, `type`, `slug`.
- **`budget_realizations`**: `id` (PK), `year`, `type`, `category_name`, `budget_amount`, `realization_amount`.
- **`posts`**: `id` (PK), `category_id` (FK), `user_id` (FK), `title`, `slug`, `content`, `image`, `is_published`, `published_at`.
- **`categories`**: `id` (PK), `name`, `slug`.
- **`announcements`**: `id` (PK), `title`, `slug`, `content`, `is_active`.
- **`galleries`**: `id` (PK), `title`, `image`, `description`, `is_active`.
- **`documents`**: `id` (PK), `title`, `file_path`, `type`, `is_public`.
- **`publications`**: `id` (PK), `title`, `file_path`, `cover_image`, `description`.
- **`datasets`**: `id` (PK), `title`, `file_path`, `description`, `download_count`.
- **`officials`**: `id` (PK), `name`, `position`, `image`, `order`.
- **`institutions`**: `id` (PK), `name`, `description`, `logo`, `leader_name`.
- **`public_facilities`**: `id` (PK), `name`, `category`, `latitude`, `longitude`, `image`.
- **`village_potentials`**: `id` (PK), `title`, `category`, `description`, `image`.
- **`services`**: `id` (PK), `name`, `description`, `requirements` (JSON), `is_active`.
- **`service_requests`**: `id` (PK), `service_id` (FK), `nik`, `name`, `tracking_code`, `status`, `notes`.
- **`complaints`**: `id` (PK), `tracking_code`, `name`, `nik`, `title`, `content`, `status`, `reply`.
- **`guest_books`**: `id` (PK), `name`, `institution`, `purpose`, `date`.
- **`visitor_logs`**: `id` (PK), `ip_address`, `user_agent`, `visited_at`.
- **`settings`**: `key` (PK), `value`.
- **`metadata`**: `key` (PK), `value`.
- **`popup_infographics`**: `id` (PK), `title`, `image`, `is_active`, `link`.
- **`users`**: `id` (PK), `name`, `email`, `password`, `role`.
