# Dokumentasi Standarisasi & Riwayat Commit Git – Proyek PATEN
## Pemantauan Transaksi & Efektivitas Niaga Kabupaten Kutai Timur

Dokumen ini memuat panduan konvensi commit (*Conventional Commits*), struktur tata kelola versi, serta rincian log commit bertahap yang merepresentasikan evolusi arsitektur sistem **PATEN**.

---

## 1. Standar Format Commit (Conventional Commits)

Format penulisan pesan commit wajib mengikuti spesifikasi standar industri:

```text
<type>(<scope>): <deskripsi singkat imperatif huruf kecil>

[bodi penjelasan rinci opsional]

[footer referensi issue / breaking changes opsional]
```

### Tipe Commit (`type`):
- **`feat`**: Penambahan fitur baru (*feature*) pada aplikasi.
- **`fix`**: Perbaikan bug atau kesalahan logika.
- **`docs`**: Perubahan atau penambahan dokumentasi (README, buku panduan, laporan testing).
- **`style`**: Perubahan format/tampilan kode yang tidak mengubah fungsionalitas (white-space, formatting, titik koma).
- **`refactor`**: Rekayasa ulang struktur kode tanpa mengubah perilaku fungsional atau menambah fitur.
- **`perf`**: Optimasi performa sistem atau query database.
- **`test`**: Penambahan atau pembaruan skenario pengujian (*automated tests*).
- **`build`**: Perubahan pada build system atau dependensi eksternal (composer, npm, vite).
- **`ci`**: Perubahan pada file konfigurasi integrasi/deployment berkelanjutan.
- **`chore`**: Pekerjaan pemeliharaan rutin (konfigurasi git, pembersihan file, setup environment awal).

### Lingkup (`scope`):
- `core`: Infrastruktur inti, basis data Supabase, middleware keamanan, base model.
- `events`: Modul pembuatan event, tema warna duotone, tiket QR & gamifikasi.
- `cms`: Modul dashboard rahasia, rundown jadwal, pembagian stan, dan scanner gate.
- `pos`: Modul pendaftaran UMKM Perbup Kutim, katalog menu, dan terminal kasir transaksi atomik.
- `spk`: Modul SPK SAW, filter guardrail 1 KK 1 Penerima, leaderboard publik, dan sinkronisasi ML Colab.
- `e2e`: Pengujian integrasi menyeluruh, uji konkurensi beban tinggi, dan audit kebocoran data.

---

## 2. Riwayat 8 Commit Bertahap Proyek PATEN

Repositori proyek PATEN diatur ke dalam **8 commit atomik bertahap** untuk mencerminkan perkembangan arsitektur yang bersih, profesional, dan dapat diaudit:

| No | Tipe & Pesan Commit | Scope & Komponen Utama | File / Direktori Terdampak |
| :-: | :--- | :--- | :--- |
| **1** | `chore: project initialization, environment setup, and security gitignore` | Inisialisasi Laravel 13, Vue 3 Inertia, konfigurasi `.gitignore` aman (proteksi kredensial), dan dependensi awal. | `.gitignore`, `composer.json`, `package.json`, `vite.config.js`, `.env.example`, `config/` |
| **2** | `feat(core): setup supabase postgresql 17 schema, uuid models, and security middleware` | Migrasi 8 tabel Supabase, aktivasi ekstensi `uuid-ossp`, implementasi UUID Eloquent models, serta middleware `VerifyCmsToken` & `VerifyPosPin`. | `database/migrations/`, `app/Models/`, `app/Http/Middleware/`, `tests/Feature/Stage1CoreInfrastructureTest.php` |
| **3** | `feat(events): public form-to-pages, dynamic color theme, qr poster, and gamification` | Form pembuatan event dinamis, generator slug & 64-char token, palet duotone CSS variables, pendaftaran tiket SVG QR, dan rate limiter. | `app/Http/Controllers/EventController.php`, `app/Http/Controllers/PublicAttendanceController.php`, `resources/js/Pages/Events/`, `tests/Feature/Stage2*` |
| **4** | `feat(cms): hidden management url, rundown crud, batch stand provisioning, and gate checkin` | Dashboard CMS berbasis token rahasia, CRUD rundown acara, batch generator nomor stan & 6 digit PIN unik, scanner check-in tiket, serta pengundian doorprize acak. | `app/Http/Controllers/CmsController.php`, `app/Services/StandProvisioningService.php`, `resources/js/Pages/Cms/`, `tests/Feature/Stage3*` |
| **5** | `feat(pos): umkm profiling perbup kutim, stand assignment, and atomic pos mini` | Validasi ketat profil UMKM (NIK, No KK 16 digit, NIB), CRUD menu katalog stan, autentikasi PIN kasir, dan transaksi atomik `lockForUpdate` anti race-condition. | `app/Http/Controllers/StandAssignmentController.php`, `app/Http/Controllers/PosController.php`, `app/Models/PosTransaction.php`, `tests/Feature/Stage4*` |
| **6** | `feat(spk): hybrid spk engine saw, 1 kk 1 penerima guardrail, colab ml sync, and leaderboard` | Service SPK SAW (5 kriteria berbobot), guardrail regulasi Perbup Kutim (1 KK 1 Penerima & NIB), leaderboard publik tersensor, dan webhook integrasi ML Colab. | `app/Services/SpkEngineService.php`, `app/Http/Controllers/SpkEvaluationController.php`, `app/Http/Controllers/MlWebhookController.php`, `ml_pipeline/`, `tests/Feature/Stage5*` |
| **7** | `test(e2e): end-to-end integration, high load concurrency 25x, and zero data leak audit` | Suite pengujian integrasi end-to-end, simulasi 25x transaksi simultan beruntun, audit nol kebocoran data sensitif (NIK/KK/WA/Token), dan hardening model Event. | `tests/Feature/Stage6EndToEndAndSecurityAuditTest.php`, `app/Models/Event.php`, `docs/stage_6_implementation.md` |
| **8** | `docs: update comprehensive readme, user operational guide, and commit documentation` | README lengkap dengan arsitektur sistem, buku panduan operasional 4 aktor, dokumentasi testing terpusat (100% PASS), dan panduan standarisasi commit. | `README.md`, `docs/COMMIT_LOG.md`, `docs/CONSOLIDATED_TESTING_REPORT.md`, `docs/` |

---

## 3. Rincian Teknis Commit 1 s/d Commit 8

### Commit 1: Inisialisasi Proyek & Perlindungan Kredensial
- **Pesan Commit:** `chore: project initialization, environment setup, and security gitignore`
- **Tujuan:** Menyiapkan struktur dasar kerangka kerja Laravel 13 + Vue 3 Inertia, serta memastikan seluruh kredensial database Supabase dan kunci rahasia (`.env`, `credential.md`, sertifikat) tidak pernah terlacak oleh Git.
- **Aturan Keamanan:** `.gitignore` mengecualikan file konfigurasi lokal, storage cache, sesi, dan direktori virtual environment Python.

### Commit 2: Arsitektur Data Inti & Middleware Keamanan (Stage 1)
- **Pesan Commit:** `feat(core): setup supabase postgresql 17 schema, uuid models, and security middleware`
- **Tujuan:** Membangun fondasi basis data Supabase PostgreSQL 17 dengan 8 tabel relasional yang saling terintegrasi menggunakan UUID v4. Menyiapkan middleware otorisasi token CMS (`VerifyCmsToken`) dan PIN kasir stan (`VerifyPosPin`).
- **Verifikasi:** Lulus 8 unit test dengan 54 asersi (100% PASS).

### Commit 3: Form-to-Pages & Gamifikasi Publik (Stage 2)
- **Pesan Commit:** `feat(events): public form-to-pages, dynamic color theme, qr poster, and gamification`
- **Tujuan:** Menyediakan antarmuka pendaftaran event publik mandiri dengan skema warna duotone, generator 64-karakter secure management token, penerbitan tiket digital instan dengan QR code SVG, dan rate limiter proteksi DDoS (30 req/menit).
- **Verifikasi:** Lulus 8 unit test dengan 48 asersi (100% PASS).

### Commit 4: CMS Hidden URL & Gate Scanner (Stage 3)
- **Pesan Commit:** `feat(cms): hidden management url, rundown crud, batch stand provisioning, and gate checkin`
- **Tujuan:** Mengimplementasikan dashboard panitia tanpa login password (berbasis token rahasia URL), batch provisioning nomor stan beserta PIN 6-digit unik, verifikasi tiket di pintu gerbang, dan algoritma pengundian doorprize acak hanya untuk pengunjung yang telah check-in.
- **Verifikasi:** Lulus 6 unit test dengan 84 asersi (100% PASS).

### Commit 5: Stand Assignment & POS Mini Kasir Atomik (Stage 4)
- **Pesan Commit:** `feat(pos): umkm profiling perbup kutim, stand assignment, and atomic pos mini`
- **Tujuan:** Validasi ketat profil pelaku usaha UMKM sesuai standar Perbup Kutim (NIK, No KK, NIB), manajemen katalog menu stan, lembar cetak menu dengan warna tema event, serta terminal POS mini kasir cepat yang menerapkan `lockForUpdate()` pada transaksi atomik.
- **Verifikasi:** Lulus 6 unit test dengan 36 asersi (100% PASS).

### Commit 6: Hybrid SPK SAW Engine & ML Webhook Sync (Stage 5)
- **Pesan Commit:** `feat(spk): hybrid spk engine saw, 1 kk 1 penerima guardrail, colab ml sync, and leaderboard`
- **Tujuan:** Membangun mesin SPK SAW presisi tinggi, mengunci *deterministic guardrail* regulasi 1 KK 1 Penerima bantuan APBD, webhook sinkronisasi klasterisasi Machine Learning dari Google Colab, serta leaderboard publik yang menyensor seluruh nominal keuangan dan data pribadi.
- **Verifikasi:** Lulus 5 unit test dengan 40 asersi (100% PASS).

### Commit 7: End-to-End Integration & Security Hardening (Stage 6)
- **Pesan Commit:** `test(e2e): end-to-end integration, high load concurrency 25x, and zero data leak audit`
- **Tujuan:** Pengujian integrasi siklus hidup penuh, uji beban konkurensi 25x transaksi kasir simultan anti race condition, audit keamanan zero data leaks pada endpoint publik, pembatasan brute force PIN, dan perlindungan token tampering.
- **Verifikasi:** Lulus 5 unit test dengan 94 asersi (100% PASS).

### Commit 8: Dokumentasi Lengkap & Rilis Produksi
- **Pesan Commit:** `docs: update comprehensive readme, user operational guide, and commit documentation`
- **Tujuan:** Menyediakan panduan lengkap pengguna untuk 4 peran aktor (Panitia, Kasir, Pengunjung, Dinas), dokumentasi arsitektur sistem, panduan instalasi & env, laporan pengujian konsolidasi (44 tests, 390 assertions, 100% PASS), dan standarisasi commit.

---

## 4. Panduan Kontribusi & Alur Kerja Git

Untuk penambahan fitur atau perbaikan kode di masa mendatang, ikuti alur standar berikut:

1. **Buat Branch Fitur Baru:**
   ```bash
   git checkout -b feat/nama-fitur-baru
   ```
2. **Pastikan Seluruh Pengujian Lulus:**
   ```bash
   php artisan test
   npm run build
   ```
3. **Format Pesan Commit Sesuai Konvensi:**
   ```bash
   git commit -m "feat(scope): deskripsi singkat fitur baru"
   ```
4. **Push ke Repositori GitHub:**
   ```bash
   git push -u origin feat/nama-fitur-baru
   ```
5. **Buat Pull Request (PR):**
   Ajukan Pull Request ke branch `main` dengan menyertakan bukti screenshot/log pengujian `100% PASS`.
