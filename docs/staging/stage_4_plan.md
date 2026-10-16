# Technical Staging Plan: STAGE 4
**Nama Tahapan:** Modul 3 – Assign Stand UMKM Perbup Attributes, Menu CRUD & Re-Export, Zero-Friction Dropdown POS Mini  
**Target Modul:** Pendaftaran UMKM ke Stand, Katalog Menu & Lembar Desain Cetak, serta Kasir Cepat POS Mini Atomik  
**Standar Pengujian:** 100% PASS pada Automated Tests (Feature, Unit, Security)  

---

## 1. Skema Database & Relasi Eloquent Models

### A. Tabel Terlibat
1. **`umkm_profiles`**:
   - Menyimpan atribut kepatuhan Perbup Kutim: `nik` (16 char), `no_kk` (16 char), `nama_pemilik`, `nama_usaha`, `nomor_wa`, `kecamatan`, `alamat_usaha`, `nib`, `has_halal`, `has_pirt`, `pernah_terima_hibah`, `tahun_terima_hibah`.
2. **`event_stands`**:
   - Menghubungkan stand dengan `umkm_id`.
   - Mengakumulasi performa niaga: `total_omzet_terekam` (numeric) dan `total_transaksi_terekam` (integer).
3. **`stand_menus`**:
   - Katalog produk stand: `nama_item`, `harga`, `is_available`.
4. **`pos_transactions`**:
   - Log transaksi individual: `event_id`, `stand_id`, `menu_id`, `qty`, `subtotal`, `metode_pembayaran` ('cash'/'qris').

---

## 2. Routing, Controllers & Middleware

### A. Daftar Rute & Endpoint
| Method | URI | Controller & Action | Middleware | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| `POST` | `/stands/{stand_id}/assign` | `StandAssignmentController@assign` | `web` | Input profil UMKM Perbup & menu awal |
| `POST` | `/pos/{stand_id}/login-pin` | `PosAuthController@login` | `web`, `throttle:10,1` | Autentikasi kasir via PIN 6 digit |
| `GET` | `/pos/{stand_id}` | `PosTerminalController@index` | `web`, `pos.pin` | Halaman terminal kasir POS Mini |
| `POST` | `/pos/{stand_id}/menus` | `StandMenuController@store` | `web`, `pos.pin` | Tambah menu baru |
| `PUT` | `/pos/{stand_id}/menus/{menu_id}` | `StandMenuController@update` | `web`, `pos.pin` | Edit menu/harga |
| `DELETE` | `/pos/{stand_id}/menus/{menu_id}` | `StandMenuController@destroy` | `web`, `pos.pin` | Hapus menu |
| `GET` | `/pos/{stand_id}/export-menu` | `StandMenuController@export` | `web` | Re-export lembar menu dengan warna event |
| `POST` | `/pos/{stand_id}/transactions` | `PosTerminalController@recordTransaction` | `web`, `pos.pin`, `throttle:60,1` | Input transaksi cepat atomik ($\le 3$ detik) |

---

## 3. Spesifikasi Payload, Validasi, & Transaksi Atomik

### A. Payload Registrasi & Assign Stand (`POST /stands/{stand_id}/assign`)
- **Validation Rules:**
  - `nik`: `required|digits:16`
  - `no_kk`: `required|digits:16`
  - `nama_pemilik`: `required|string|max:150`
  - `nama_usaha`: `required|string|max:150`
  - `nomor_wa`: `required|string|max:20`
  - `kecamatan`: `required|string|max:100`
  - `alamat_usaha`: `nullable|string`
  - `nib`: `nullable|string|max:50`
  - `has_halal`: `boolean`
  - `has_pirt`: `boolean`
  - `pernah_terima_hibah`: `boolean`
  - `tahun_terima_hibah`: `nullable|integer`
  - `initial_menus`: `nullable|array`

### B. Payload Transaksi POS Mini (`POST /pos/{stand_id}/transactions`)
- **Validation Rules:**
  - `menu_id`: `required|uuid|exists:stand_menus,id`
  - `qty`: `required|integer|min:1|max:100`
  - `metode_pembayaran`: `required|in:cash,qris`
- **Operasi Database Atomik (`DB::transaction`):**
  1. Kunci baris stand (`lockForUpdate()`).
  2. Ambil harga menu aktif (`harga`).
  3. Hitung `subtotal = harga * qty`.
  4. Masukkan log ke tabel `pos_transactions`.
  5. Perbarui agregat stand:
     - `total_omzet_terekam = total_omzet_terekam + subtotal`
     - `total_transaksi_terekam = total_transaksi_terekam + 1`
- **Response Success (201 Created):**
  ```json
  {
    "success": true,
    "subtotal": 25000.00,
    "total_omzet_stand": 350000.00,
    "total_transaksi_stand": 14
  }
  ```

---

## 4. Komponen Raw Frontend (Inertia Vue 3)

1. `resources/js/Pages/Stands/Assign.vue`:
   - Narasi edukasi/disclaimer: "Pencatatan transaksi ini adalah portofolio verifikasi kurasi bantuan UMKM Kutim, BUKAN untuk dasar penarikan pajak daerah."
   - `<form>` input NIK (16 digit), No KK, Nama Pemilik, NIB, Checkbox Halal/PIRT.
   - Form input menu awal (Nama Menu, Harga).
2. `resources/js/Pages/Pos/Login.vue`:
   - `<form>` input PIN Kasir 6-digit.
3. `resources/js/Pages/Pos/Terminal.vue`:
   - Antarmuka super ringkas:
     - `<select>` Dropdown Menu (otomatis menampilkan nama dan harga).
     - `<input type="number">` Qty (default: 1).
     - `<select>` Metode Bayar (`Tunai` / `QRIS`).
     - `<button>` "Simpan Transaksi (Enter / Tap)".
   - `<table>` Riwayat 5 transaksi terakhir untuk verifikasi instan.

---

## 5. Skenario Pengujian Otomatis (Wajib 100% PASS)

File pengujian: `tests/Feature/Stage4StandAssignmentAndPosMiniTest.php`
1. `test_umkm_assignment_validates_nik_and_kk_format`: NIK dan KK yang bukan 16 digit memicu validasi 422.
2. `test_pos_pin_authentication_sets_session_or_returns_error`: Login dengan PIN yang benar meloloskan akses kasir, PIN salah ditolak (403).
3. `test_stand_menu_crud_operations`: Menambah, mengubah harga, dan menghapus item menu stand.
4. `test_pos_transaction_atomic_execution`: Transaksi POS berhasil meng-insert baris ke `pos_transactions` dan meng-increment `total_omzet_terekam` serta `total_transaksi_terekam` secara konsisten.
5. `test_pos_transaction_prevents_unauthorized_stand_tampering`: Transaksi ditolak jika stand_id pada URL berbeda dengan stand_id sesi kasir.
6. `test_menu_export_includes_induk_event_colors`: Endpoint `/pos/{stand_id}/export-menu` memuat daftar menu dan variabel tema warna event.
