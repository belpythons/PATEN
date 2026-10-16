# Technical Staging Plan: STAGE 3
**Nama Tahapan:** Modul 2 – CMS Hidden URL Token Auth, Rundown Extender CRUD, Stand & Facility Provisioning, Gate Check-in & Doorprize Picker  
**Target Modul:** Dashboard Pengelola Acara via Token Rahasia, Modifikasi Rundown Lapangan, Penyediaan Slot Stand & PIN Kasir, serta Operasional Gate  
**Standar Pengujian:** 100% PASS pada Automated Tests (Feature, Unit, Security)  

---

## 1. Skema Database & Relasi Eloquent Models

### A. Tabel Terlibat
1. **`events`**: Validasi `secure_management_token` dan pembacaan konfigurasi acara.
2. **`event_rundowns`**: CRUD sesi jadwal (`judul_sesi`, `waktu_mulai`, `waktu_selesai`, `pengisi_acara`, `urutan`).
3. **`event_stands`**:
   - Penyediaan slot stand baru (`nomor_stand`, `kategori_jualan`, `daya_listrik_watt`, `fasilitas_detail`, `pin_pos`).
   - `pin_pos`: 6 digit angka acak (misal: `491823`) untuk autentikasi kasir POS.
   - `fasilitas_detail`: JSONB menyimpan `{"meja": 1, "kursi": 2, "tenda": "Sarnafil 3x3", "air_bersih": true}`.
4. **`attendees`**:
   - Pembaruan status gate check-in: `is_checked_in = TRUE`, `check_in_time = NOW()`.
   - Pemilihan pemenang doorprize: `is_doorprize_winner = TRUE`.

---

## 2. Routing, Controllers & Middleware

Seluruh endpoint pada Modul 2 dilindungi oleh `VerifyCmsTokenMiddleware`.

### A. Daftar Rute & Endpoint
| Method | URI | Controller & Action | Middleware | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/cms/{slug}` | `CmsDashboardController@show` | `web`, `cms.token` | Dashboard utama CMS Hidden URL |
| `POST` | `/cms/{slug}/rundowns` | `CmsRundownController@store` | `web`, `cms.token` | Tambah sesi jadwal rundown baru |
| `PUT` | `/cms/{slug}/rundowns/{id}` | `CmsRundownController@update` | `web`, `cms.token` | Perbarui waktu / detail sesi rundown |
| `DELETE` | `/cms/{slug}/rundowns/{id}` | `CmsRundownController@destroy` | `web`, `cms.token` | Hapus sesi rundown |
| `POST` | `/cms/{slug}/stands` | `CmsStandController@provision` | `web`, `cms.token` | Batch / single generator slot stand & PIN |
| `POST` | `/cms/{slug}/check-in` | `CmsGateController@checkIn` | `web`, `cms.token` | Verifikasi kedatangan tiket di gate |
| `POST` | `/cms/{slug}/draw-doorprize` | `CmsGateController@drawDoorprize` | `web`, `cms.token` | Undian acak pemenang doorprize dari peserta hadir |

---

## 3. Spesifikasi Payload, Validasi, & Struktur Response

### A. Payload Provisioning Stand (`POST /cms/{slug}/stands`)
- **Validation Rules:**
  - `jumlah_stand`: `required|integer|min:1|max:100`
  - `kategori_jualan`: `required|in:kuliner,kriya,fesyen,agribisnis,jasa`
  - `daya_listrik_watt`: `required|integer|min:0`
  - `fasilitas_detail`: `nullable|array`
- **Response Success (201 Created):**
  ```json
  {
    "success": true,
    "message": "10 stand berhasil dibuat beserta PIN kasir.",
    "stands": [
      {
        "id": "uuid",
        "nomor_stand": "Stand 01",
        "kategori_jualan": "kuliner",
        "pin_pos": "382910"
      }
    ]
  }
  ```

### B. Payload Gate Check-in (`POST /cms/{slug}/check-in`)
- **Validation Rules:**
  - `nomor_tiket_doorprize`: `required|string`
- **Response Success (200 OK):**
  ```json
  {
    "success": true,
    "message": "Check-in berhasil.",
    "attendee": {
      "nama_lengkap": "Budi Santoso",
      "tipe_registrasi": "doorprize",
      "check_in_time": "2026-10-17T19:30:00+08:00"
    }
  }
  ```

### C. Payload Undi Doorprize (`POST /cms/{slug}/draw-doorprize`)
- **Validation Rules:**
  - `jumlah_pemenang`: `required|integer|min:1|max:10`
- **Response Success (200 OK):**
  Mengembalikan array pemenang yang statusnya `is_checked_in == TRUE` dan belum pernah menang.

---

## 4. Komponen Raw Frontend (Inertia Vue 3)

1. `resources/js/Pages/Cms/Dashboard.vue`:
   - Ringkasan metrik: total stand aktif, total pengunjung check-in, estimasi total omzet.
   - Manajemen Rundown: `<form>` tambah jadwal baru, `<table>` daftar sesi dengan tombol edit dan hapus.
   - Generator Stand: `<form>` kuota stand, dropdown kategori, input watt listrik, dan tombol "Generate Stand".
   - `<table>` Daftar Stand lengkap dengan nomor stand dan kode PIN 6 digit untuk dibagikan ke pedagang.
   - Gate Scanner: `<form>` input nomor kupon/tiket + tombol "Check-in".
   - Doorprize Engine: Tombol "Undi Pemenang Acak" + `<table>` daftar pemenang terpilih.

---

## 5. Skenario Pengujian Otomatis (Wajib 100% PASS)

File pengujian: `tests/Feature/Stage3CmsManagementAndGateTest.php`
1. `test_cms_dashboard_requires_valid_secret_token`: Mengakses tanpa token atau token salah ditolak (401/403).
2. `test_cms_rundown_crud_operations`: Menambah, mengedit waktu, dan menghapus sesi rundown pada event aktif.
3. `test_batch_stand_provisioning_generates_unique_six_digit_pins`: Memastikan 20 stand yang digenerate masing-masing memiliki `pin_pos` 6 digit numerik unik.
4. `test_gate_check_in_updates_attendee_status`: Input tiket yang valid memperbarui `is_checked_in` dan `check_in_time`.
5. `test_gate_check_in_rejects_duplicate_or_invalid_ticket`: Tiket yang sudah check-in memberikan peringatan dan tiket fiktif menghasilkan 404.
6. `test_doorprize_drawing_only_picks_checked_in_attendees`: Pemenang undian hanya berasal dari pengunjung yang berstatus `is_checked_in == true`.
