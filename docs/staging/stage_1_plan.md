# Technical Staging Plan: STAGE 1
**Nama Tahapan:** Core Infrastructure, Supabase DDL Migrations, Eloquent Models & Security Middleware  
**Target Modul:** Infrastruktur Inti, Lapisan Data, dan Proteksi Akses Token/PIN  
**Standar Pengujian:** 100% PASS pada Automated Tests (Feature, Unit, Security)  

---

## 1. Skema Database & Relasi Eloquent Models

### A. Tabel Database Supabase yang Terlibat (Single Source of Truth)
1. **`events`**:
   - `id` (UUID, Primary Key, default `uuid_generate_v4()`)
   - `slug` (VARCHAR(120), UNIQUE, NOT NULL)
   - `nama_event` (VARCHAR(255), NOT NULL)
   - `deskripsi` (TEXT, NOT NULL)
   - `penyelenggara` (VARCHAR(150), NOT NULL)
   - `lokasi_venue` (VARCHAR(255), NOT NULL)
   - `tanggal_mulai` (TIMESTAMP WITH TIME ZONE, NOT NULL)
   - `tanggal_selesai` (TIMESTAMP WITH TIME ZONE, NOT NULL)
   - `secure_management_token` (VARCHAR(64), UNIQUE, NOT NULL)
   - `template_theme` (VARCHAR(50), DEFAULT 'kutim_formal')
   - `color_mode` (VARCHAR(10), DEFAULT 'duo')
   - `color_primary` (VARCHAR(7), DEFAULT '#0F766E')
   - `color_secondary` (VARCHAR(7), DEFAULT '#F59E0B')
   - `poster_asset_url` (TEXT, NULLABLE)
   - `gamifikasi_mode` (ENUM `attendance_mode_enum`: 'none', 'htm', 'doorprize', 'both')
   - `harga_htm` (NUMERIC(12, 2), DEFAULT 0.00)
   - `is_awarding_active` (BOOLEAN, DEFAULT TRUE)
   - `created_at`, `updated_at` (TIMESTAMPTZ)

2. **`event_rundowns`**:
   - `id` (UUID, PK), `event_id` (UUID, FK `events.id` ON DELETE CASCADE)
   - `waktu_mulai`, `waktu_selesai` (TIMESTAMPTZ)
   - `judul_sesi` (VARCHAR(200)), `pengisi_acara` (VARCHAR(150)), `deskripsi_sesi` (TEXT), `urutan` (INT)

3. **`umkm_profiles`**:
   - `id` (UUID, PK), `nik` (VARCHAR(16), UNIQUE), `no_kk` (VARCHAR(16))
   - `nama_pemilik` (VARCHAR(150)), `nama_usaha` (VARCHAR(150)), `nomor_wa` (VARCHAR(20)), `kecamatan` (VARCHAR(100)), `alamat_usaha` (TEXT)
   - `nib` (VARCHAR(50), NULLABLE), `has_halal` (BOOLEAN), `has_pirt` (BOOLEAN), `pernah_terima_hibah` (BOOLEAN), `tahun_terima_hibah` (INT, NULLABLE)

4. **`event_stands`**:
   - `id` (UUID, PK), `event_id` (UUID, FK `events.id` ON DELETE CASCADE), `umkm_id` (UUID, FK `umkm_profiles.id` ON DELETE SET NULL)
   - `nomor_stand` (VARCHAR(20)), `kategori_jualan` (ENUM `business_category_enum`: 'kuliner', 'kriya', 'fesyen', 'agribisnis', 'jasa')
   - `daya_listrik_watt` (INT), `fasilitas_detail` (JSONB), `pin_pos` (VARCHAR(6))
   - `total_omzet_terekam` (NUMERIC(14, 2), DEFAULT 0.00), `total_transaksi_terekam` (INT, DEFAULT 0)

5. **`stand_menus`**:
   - `id` (UUID, PK), `stand_id` (UUID, FK `event_stands.id` ON DELETE CASCADE)
   - `nama_item` (VARCHAR(150)), `harga` (NUMERIC(12, 2)), `is_available` (BOOLEAN, DEFAULT TRUE)

6. **`pos_transactions`**:
   - `id` (UUID, PK), `event_id` (UUID, FK `events.id`), `stand_id` (UUID, FK `event_stands.id`), `menu_id` (UUID, FK `stand_menus.id`)
   - `qty` (INT), `subtotal` (NUMERIC(12, 2)), `metode_pembayaran` (ENUM `payment_mode_enum`: 'cash', 'qris'), `created_at` (TIMESTAMPTZ)

7. **`attendees`**:
   - `id` (UUID, PK), `event_id` (UUID, FK `events.id` ON DELETE CASCADE)
   - `nama_lengkap` (VARCHAR(150)), `nomor_wa` (VARCHAR(20)), `kecamatan` (VARCHAR(100))
   - `nomor_tiket_doorprize` (VARCHAR(40), UNIQUE), `tipe_registrasi` (ENUM `attendance_mode_enum`), `is_checked_in` (BOOLEAN), `check_in_time` (TIMESTAMPTZ), `is_doorprize_winner` (BOOLEAN)

8. **`spk_umkm_evaluations`**:
   - `id` (UUID, PK), `event_id` (UUID, FK `events.id`), `umkm_id` (UUID, FK `umkm_profiles.id`), `stand_id` (UUID, FK `event_stands.id`)
   - `score_konsistensi_transaksi`, `score_digitalisasi_qris`, `score_kepatuhan_regulasi`, `skor_akhir_spk` (NUMERIC)
   - `cluster_kategori` (VARCHAR(50)), `rekomendasi_program` (ENUM `spk_recommendation_enum`: 'pending', 'fasilitasi_legalitas', 'hibah_alat', 'hibah_modal_apbd', 'kredit_perbankan')
   - `is_prioritas_hibah_200` (BOOLEAN), `catatan_dinas` (TEXT), `calculated_at` (TIMESTAMPTZ)

### B. Relasi Eloquent Models (`app/Models/`)
- `Event`:
  - `hasMany(EventRundown::class)`
  - `hasMany(EventStand::class)`
  - `hasMany(Attendee::class)`
  - `hasMany(PosTransaction::class)`
  - `hasMany(SpkUmkmEvaluation::class)`
- `UmkmProfile`:
  - `hasMany(EventStand::class)`
  - `hasMany(SpkUmkmEvaluation::class)`
- `EventStand`:
  - `belongsTo(Event::class)`
  - `belongsTo(UmkmProfile::class)`
  - `hasMany(StandMenu::class, 'stand_id')`
  - `hasMany(PosTransaction::class, 'stand_id')`
  - `hasOne(SpkUmkmEvaluation::class, 'stand_id')`
  - Casts: `'fasilitas_detail' => 'array'`, `'total_omzet_terekam' => 'decimal:2'`
- `StandMenu`:
  - `belongsTo(EventStand::class, 'stand_id')`
  - `hasMany(PosTransaction::class, 'menu_id')`
- `PosTransaction`:
  - `belongsTo(Event::class)`
  - `belongsTo(EventStand::class, 'stand_id')`
  - `belongsTo(StandMenu::class, 'menu_id')`
- `Attendee`:
  - `belongsTo(Event::class)`
- `SpkUmkmEvaluation`:
  - `belongsTo(Event::class)`
  - `belongsTo(EventStand::class, 'stand_id')`
  - `belongsTo(UmkmProfile::class, 'umkm_id')`

---

## 2. Routing, Controllers & Middleware Keamanan

### A. Rute Verifikasi Fondasi
- `GET /health` -> `HealthCheckController@index` (Menampilkan status koneksi Supabase & versi PostgreSQL).

### B. Middleware Keamanan
1. **`VerifyCmsTokenMiddleware` (`app/Http/Middleware/VerifyCmsTokenMiddleware.php`)**:
   - Memeriksa parameter query `token` atau header `X-CMS-Token` pada rute `/cms/{slug}`.
   - Mengambil event berdasarkan `slug` dan mencocokkan dengan `secure_management_token`.
   - Mengembalikan HTTP 401 jika token hilang, dan HTTP 403 jika token tidak valid.
2. **`VerifyPosPinMiddleware` (`app/Http/Middleware/VerifyPosPinMiddleware.php`)**:
   - Memeriksa sesi stand (`pos_stand_id`) atau header `X-Stand-Pin` pada rute `/pos/{stand_id}/*`.
   - Memastikan `pin_pos` cocok dengan nomor stand yang bersangkutan.
   - Mengembalikan HTTP 401/403 jika otorisasi stand gagal.
3. **Konfigurasi Rate Limiting (`bootstrap/app.php` / RouteServiceProvider)**:
   - Rate limit 30 request/menit untuk pendaftaran publik `/e/{slug}/register-attendance`.
   - Rate limit 60 request/menit untuk transaksi kasir `/pos/{stand_id}/transactions`.

---

## 3. Spesifikasi Payload, Validasi, & Struktur Response

### A. Health Check Response (`GET /health`)
- Response Success (200 OK):
  ```json
  {
    "status": "healthy",
    "database": {
      "connection": "pgsql",
      "version": "PostgreSQL 17.11...",
      "tables_count": 8,
      "uuid_ossp": "active"
    },
    "timestamp": "2026-10-17T03:50:00+08:00"
  }
  ```

---

## 4. Komponen Raw Frontend (Inertia Vue 3)

Pada Stage 1 tidak ada antarmuka fitur bisnis. Komponen yang disediakan murni untuk verifikasi:
- `resources/js/Pages/Welcome.vue`:
  - Elemen: `<p>` status koneksi Supabase, `<table>` daftar 8 tabel aktif dan jumlah record.

---

## 5. Skenario Pengujian Otomatis (Wajib 100% PASS)

File pengujian: `tests/Feature/Stage1CoreInfrastructureTest.php`
1. `test_supabase_connection_and_table_schema_integrity`: Memastikan 8 tabel terhubung dan query count berjalan tanpa error.
2. `test_uuid_generation_works_seamlessly`: Memastikan `uuid_generate_v4()` menghasilkan UUID valid.
3. `test_eloquent_models_can_instantiate_and_relate`: Membuat data dummy di Supabase menggunakan Eloquent dan memverifikasi relasi foreign key.
4. `test_verify_cms_token_middleware_rejects_missing_token`: Akses rute `/cms/test-event` tanpa token menghasilkan 401 Unauthorized.
5. `test_verify_cms_token_middleware_rejects_invalid_token`: Akses rute `/cms/test-event?token=wrong_token` menghasilkan 403 Forbidden.
6. `test_verify_cms_token_middleware_allows_valid_token`: Akses rute dengan `secure_management_token` yang benar menghasilkan 200 OK.
7. `test_verify_pos_pin_middleware_enforces_correct_pin`: Endpoint POS Mini menolak PIN stand yang salah (403) dan meloloskan PIN stand yang benar (200).
