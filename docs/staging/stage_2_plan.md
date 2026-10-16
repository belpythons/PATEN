# Technical Staging Plan: STAGE 2
**Nama Tahapan:** Modul 1 – Form-to-Pages Event Generator, Lock Confirmation, Color Theme, Poster Export & Gamifikasi Publik  
**Target Modul:** Pembuatan Event, Microsite Publik `/e/{slug}`, Pendaftaran Kehadiran (HTM & Doorprize), dan Generator Poster QR  
**Standar Pengujian:** 100% PASS pada Automated Tests (Feature, Unit, Security)  

---

## 1. Skema Database & Relasi Eloquent Models

### A. Tabel Terlibat
1. **`events`**:
   - Kolom utama yang diproses: `slug`, `nama_event`, `deskripsi`, `penyelenggara`, `lokasi_venue`, `tanggal_mulai`, `tanggal_selesai`, `secure_management_token`, `template_theme`, `color_mode`, `color_primary`, `color_secondary`, `gamifikasi_mode`, `harga_htm`, `is_awarding_active`.
2. **`event_rundowns`**:
   - Menyimpan rundown awal yang diinput bersamaan dengan form pembuatan event (`waktu_mulai`, `waktu_selesai`, `judul_sesi`, `pengisi_acara`, `urutan`).
3. **`attendees`**:
   - Menyimpan pencatatan pendaftaran pengunjung: `nama_lengkap`, `nomor_wa`, `kecamatan`, `nomor_tiket_doorprize` (format: `PATEN-KUTIM-{RANDOM}`), `tipe_registrasi` ('htm'/'doorprize'), `is_checked_in` (default FALSE).

---

## 2. Routing, Controllers & Middleware

### A. Daftar Rute & Endpoint
| Method | URI | Controller & Action | Middleware | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/events/create` | `EventController@create` | `web` | Render form input pembuatan event Inertia |
| `POST` | `/events` | `EventController@store` | `web` | Validasi & simpan event, generate token 64-char & slug |
| `GET` | `/e/{slug}` | `PublicEventController@show` | `web` | Render landing page publik jadwal & stand |
| `POST` | `/e/{slug}/register-attendance` | `PublicEventController@registerAttendance` | `web`, `throttle:30,1` | Pendaftaran tiket HTM / kupon doorprize |
| `GET` | `/events/{slug}/poster-export` | `PosterGeneratorController@export` | `web` | Render data poster, warna tema, & QR code publik |

---

## 3. Spesifikasi Payload, Validasi, & Struktur Response

### A. Payload Pembuatan Event (`POST /events`)
- **Validation Rules:**
  - `nama_event`: `required|string|max:255`
  - `deskripsi`: `required|string`
  - `penyelenggara`: `required|string|max:150`
  - `lokasi_venue`: `required|string|max:255`
  - `tanggal_mulai`: `required|date|before_or_equal:tanggal_selesai`
  - `tanggal_selesai`: `required|date|after_or_equal:tanggal_mulai`
  - `color_mode`: `required|in:mono,duo`
  - `color_primary`: `required|regex:/^#[0-9A-Fa-f]{6}$/`
  - `color_secondary`: `nullable|regex:/^#[0-9A-Fa-f]{6}$/`
  - `gamifikasi_mode`: `required|in:none,htm,doorprize,both`
  - `harga_htm`: `required_if:gamifikasi_mode,htm,both|numeric|min:0`
  - `is_awarding_active`: `boolean`
  - `initial_rundowns`: `nullable|array`
  - `initial_rundowns.*.judul_sesi`: `required|string|max:200`
  - `initial_rundowns.*.waktu_mulai`: `required|date`
  - `initial_rundowns.*.waktu_selesai`: `required|date`
- **Response Success (201 Created):**
  ```json
  {
    "success": true,
    "message": "Event berhasil dibuat.",
    "data": {
      "id": "uuid",
      "slug": "pekan-raya-kutim-2026",
      "public_url": "http://localhost:8000/e/pekan-raya-kutim-2026",
      "cms_manage_url": "http://localhost:8000/cms/pekan-raya-kutim-2026?token=64_char_token",
      "secure_management_token": "64_char_token"
    }
  }
  ```

### B. Payload Registrasi Kehadiran (`POST /e/{slug}/register-attendance`)
- **Validation Rules:**
  - `nama_lengkap`: `required|string|max:150`
  - `nomor_wa`: `required|string|max:20`
  - `kecamatan`: `required|string|max:100`
  - `tipe_registrasi`: `required|in:htm,doorprize`
- **Response Success (201 Created):**
  ```json
  {
    "success": true,
    "message": "Pendaftaran kehadiran berhasil.",
    "ticket": {
      "nomor_tiket_doorprize": "PATEN-DOOR-8F3A19C2",
      "nama_lengkap": "Budi Santoso",
      "tipe_registrasi": "doorprize",
      "qr_payload": "PATEN-DOOR-8F3A19C2"
    }
  }
  ```

---

## 4. Komponen Raw Frontend (Inertia Vue 3)

1. `resources/js/Pages/Events/Create.vue`:
   - `<form>` pembuatan event dengan `<input>` teks, tanggal, color picker.
   - `<select>` pilihan mode gamifikasi dan mode warna (`mono`/`duo`).
   - Modal Konfirmasi (Raw `<dialog>` / `<div>` conditional): Menampilkan ringkasan data sebelum tombol final submit diklik.
2. `resources/js/Pages/Events/Show.vue`:
   - `<h1>` Nama Event, `<p>` Lokasi, Waktu, dan Penyelenggara.
   - `<table>` Rundown jadwal acara.
   - `<form>` Pendaftaran pengunjung (nama, nomor WA, kecamatan).
   - Display kupon/tiket hasil pendaftaran.

---

## 5. Skenario Pengujian Otomatis (Wajib 100% PASS)

File pengujian: `tests/Feature/Stage2EventCreationAndPublicGamificationTest.php`
1. `test_event_creation_validates_required_fields`: Memastikan input kosong memicu validasi HTTP 422.
2. `test_event_creation_generates_unique_slug_and_secure_token`: Memastikan `slug` otomatis dibuat unik dan `secure_management_token` sepanjang 64 karakter tersimpan.
3. `test_public_event_page_renders_with_event_data_and_rundown`: Mengakses `GET /e/{slug}` merender halaman dengan data event dan array rundown.
4. `test_public_attendance_registration_creates_unique_ticket`: Pendaftaran pengunjung menyimpan data ke tabel `attendees` dengan nomor tiket unik.
5. `test_attendance_registration_rate_limiting`: Mencegah pendaftaran spam (>30 request/menit) dengan respons HTTP 429.
6. `test_poster_export_endpoint_returns_valid_data`: Endpoint `/events/{slug}/poster-export` mengembalikan konfigurasi tema warna dan QR code data URL.
