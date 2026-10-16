# Breakdown Planning Implementasi & Stage-by-Stage Pengerjaan
**Nama Proyek:** PATEN (Pemantauan Transaksi & Efektivitas Niaga)  
**Pendekatan Eksekusi:** Backend-First, Security & Networking Validation, Raw Functional Frontend (Inertia.js tanpa fokus styling estetika terlebih dahulu untuk memastikan seluruh logika bisnis, database, dan integrasi ML berjalan stabil).

---

## Ringkasan Tahapan (Stages Overview)

| Stage | Fokus Modul | Komponen Utama yang Dibangun | Target Validasi & Testing |
| :--- | :--- | :--- | :--- |
| **Stage 1** | Core Infrastructure, Database Schema & Security Networking | Konfigurasi Laravel + Inertia + Supabase PostgreSQL, Migrations, Models, Token Auth Middleware, Rate Limiting. | Koneksi DB stabil, migrasi tanpa error, proteksi route dan validasi koneksi Supabase. |
| **Stage 2** | Modul 1: Form-to-Pages Event Generator, Poster QR & Gamifikasi Publik | CRUD Form Event, Lock Confirmation, Color Theme Config (`mono`/`duo`), QR Code & Poster Generator API, Form HTM & Absensi Doorprize. | Pembuatan event menghasilkan URL publik `/e/{slug}`, file poster ber-QR, dan input kupon doorprize/HTM. |
| **Stage 3** | Modul 2: CMS Hidden URL Penyelenggara & Stand Setup | Middleware `cms_secret_token`, CRUD & Extend Rundown, Setup Kuota Stand + JSON Fasilitas, Gate Check-in Scanner & Doorprize Picker. | Akses CMS via hidden token, tambah/ubah rundown tampil real-time, generate slot stand dan undi doorprize. |
| **Stage 4** | Modul 3: Assign Stand UMKM, Menu Designer & POS Mini | Registrasi UMKM (atribut Perbup Kutim), Validasi NIK/KK, CRUD Menu Stand, Export/Re-export Menu, POS Mini (Dropdown Menu + Metode Bayar). | Assign UMKM ke stand, input transaksi via dropdown menu mengupdate `total_omzet` & `poin_keaktifan` secara atomik. |
| **Stage 5** | Modul 4: SPK Engine, Integrasi ML Pipeline & Awarding Leaderboard | Endpoint Trigger Evaluasi SPK/ML, Guardrail Perbup Kutim, Sinkronisasi Hasil ML Google Colab/Python, Leaderboard Awarding (Top 1/3/5) tanpa ekspos omzet ke publik. | Akurasi filter Perbup (1 KK 1 penerima, NIB $\ge 6$ bln), akurasi klasterisasi 5 program bantuan Kutim, dan agregasi laporan di CMS. |
| **Stage 6** | End-to-End Integration, Stress Testing & Security Hardening | Concurrency testing pada POS Mini, validasi transaksi atomik, audit keamanan endpoint API, finalisasi dokumentasi testing gabungan. | Seluruh alur dari pembuatan event hingga keluarnya rekomendasi bantuan UMKM lulus uji beban dan keamanan. |

---

## Detail Rincian Pengerjaan Per Stage

### STAGE 1: Core Infrastructure, Database Schema & Security Foundation
1. **Database & Models:**
   * Implementasi seluruh skema tabel PostgreSQL di Supabase (`events`, `event_rundowns`, `umkm_profiles`, `event_stands`, `stand_menus`, `pos_transactions`, `event_attendees`, `spk_umkm_evaluations`).
   * Pembuatan Eloquent Models beserta relasi lengkap dan casting atribut JSONB (`daftar_sponsor`, `fasilitas`).
2. **Networking & Security Middleware:**
   * Pembuatan `VerifyCmsTokenMiddleware` untuk memvalidasi akses ke Hidden URL CMS Penyelenggara.
   * Pembuatan `VerifyPosPinMiddleware` (session/token berbasis PIN 6 digit per stand) untuk mengamankan endpoint transaksi POS Mini.
   * Konfigurasi CORS dan Rate Limiting pada endpoint publik (pendaftaran doorprize dan input transaksi).

### STAGE 2: Modul 1 – Form-to-Pages Event Generator, Poster QR & Gamifikasi Publik
1. **Event Creation & Theme Engine:**
   * Endpoint `POST /events` dengan validasi input menyeluruh (deskripsi, penyelenggara, sponsor, lokasi, waktu, rundown awal).
   * Penyimpanan konfigurasi warna desain (`color_mode`: `mono` atau `duo`, `primary_color`, `secondary_color`) dan opsi gamifikasi (`attendance_mode`, `harga_htm`, `awarding_mode`).
   * Auto-generate `slug` unik dan `cms_secret_token` (64-char cryptographic random string).
2. **Dynamic Poster & Barcode/QR Generator:**
   * Service backend untuk menghasilkan QR Code yang mengarah ke `APP_URL/e/{slug}`.
   * Endpoint generator poster event (`GET /events/{slug}/poster-export`) yang merender informasi acara + warna tema + QR Code.
3. **Public Event Page & Gamifikasi Kehadiran (Raw Inertia View):**
   * Endpoint publik `GET /e/{slug}` yang memuat jadwal kegiatan, daftar stand, klasemen poin keaktifan UMKM (jika diaktifkan), dan form kehadiran.
   * Endpoint `POST /e/{slug}/register-attendance` untuk mencatat pembelian tiket HTM atau pendaftaran kupon doorprize (`kode_tiket_kupon` unik).

### STAGE 3: Modul 2 – CMS Hidden URL Penyelenggara & Manajemen Stand
1. **Hidden URL Dashboard:**
   * Route `GET /cms/{slug}?token={cms_secret_token}` untuk menampilkan ringkasan metrik event.
2. **Rundown Extender (CRUD):**
   * Endpoint `POST /cms/{slug}/rundowns`, `PUT /cms/{slug}/rundowns/{id}`, dan `DELETE /cms/{slug}/rundowns/{id}` untuk mengubah atau memperpanjang jadwal kegiatan sewaktu-waktu.
3. **Stand & Facility Provisioning:**
   * Endpoint batch/single generator stand (`POST /cms/{slug}/stands`) dengan pengaturan fasilitas (Watt listrik, meja, kursi, air bersih, jenis tenda) dan auto-generate `pin_akses_pos` 6 digit.
4. **Gate Check-in & Doorprize Engine:**
   * Endpoint `POST /cms/{slug}/check-in` untuk memvalidasi kehadiran tiket HTM/kupon di lapangan.
   * Endpoint `POST /cms/{slug}/draw-doorprize` untuk mengundi pemenang doorprize secara acak dari daftar peserta yang hadir.

### STAGE 4: Modul 3 – Assign Stand UMKM, Menu Designer & POS Mini
1. **Assign Stand & Registrasi UMKM (Kepatuhan Perbup Kutim):**
   * Endpoint `POST /stands/{stand_id}/assign` untuk mendaftarkan biodata UMKM (`nik`, `no_kk`, `lama_nib_bulan`, status halal/PIRT/HKI, riwayat hibah) sekaligus menginput menu awal.
   * Penyertaan teks narasi/disclaimer resmi program bantuan UMKM Kutai Timur pada response/view.
2. **CRUD Menu & Re-Export Desain Menu:**
   * Endpoint `POST /pos/{stand_id}/menus`, `PUT /pos/{stand_id}/menus/{menu_id}`, `DELETE /pos/{stand_id}/menus/{menu_id}`.
   * Endpoint `GET /pos/{stand_id}/export-menu` yang merender lembar daftar menu beserta harga dengan menerapkan warna `primary_color` dan `secondary_color` dari event induk.
3. **Zero-Friction POS Mini Engine:**
   * Autentikasi cepat stand menggunakan `kode_stand` + `pin_akses_pos`.
   * Endpoint `POST /pos/{stand_id}/transactions` yang hanya membutuhkan payload `menu_id`, `qty`, dan `metode_pembayaran` (`cash`/`qris`).
   * Penggunaan Database Transaction (`DB::transaction`) untuk menyimpan log ke `pos_transactions` sekaligus meng-increment `total_transaksi`, `total_omzet`, dan `poin_keaktifan` pada `event_stands`.

### STAGE 5: Modul 4 – SPK Terintegrasi Machine Learning & Awarding UMKM
1. **Awarding UMKM Engine:**
   * Kalkulasi klasemen Top 1 / Top 3 / Top 5 stand paling aktif berdasarkan frekuensi dan poin transaksi.
   * Pemisahan visibilitas data: Endpoint publik **hanya** mengirim nama stand dan poin/peringkat (tanpa nominal rupiah), sedangkan endpoint CMS Pengelola menampilkan omzet riil dan detail transaksi.
2. **SPK Rule-Based & ML Pipeline Integration:**
   * Endpoint `POST /cms/{slug}/evaluate-spk` dan API Webhook `/api/ml/sync-evaluations` untuk jembatan komunikasi antara Laravel, Supabase, dan script Python/Google Colab.
   * Implementasi logika deterministik Perbup Kutim (cek duplikasi `no_kk`, domisili, NIB $\ge 6$ bulan, bebas status ASN, belum pernah terima hibah) dan penyimpanan rekomendasi 5 program bantuan ke tabel `spk_umkm_evaluations`.

### STAGE 6: End-to-End Integration, Security Audit & Consolidated Testing
1. **Pengujian Integrasi & Beban:**
   * Simulasi seluruh alur dari pembuatan event, pendaftaran 30+ stand UMKM, ratusan transaksi POS simultan, hingga penarikan laporan SPK/ML di halaman CMS Pengelola.
2. **Audit Keamanan:**
   * Pengujian kebocoran data sensitif (memastikan NIK, No KK, dan nominal omzet tidak bocor di JSON response halaman publik `/e/{slug}`).