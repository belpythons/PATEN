# Koleksi Prompt Antigravity – Proyek PATEN Kutai Timur

Dokumen ini berisi **3 Prompt Utama** yang dirancang khusus untuk dijalankan secara berurutan pada **Antigravity**:
* **Prompt 1 (Project, DB & MCP Setup):** Digunakan pada awal inisialisasi untuk men-setup project Laravel + Inertia.js, membaca `credential.md`, menghubungkan environment Supabase PostgreSQL, serta mengonfigurasi MCP (Model Context Protocol).
* **Prompt 2 (Technical Breakdown & Governance Artifacts):** Digunakan tepat setelah Prompt 1 selesai untuk menerjemahkan Implementation Plan ke dalam file rencana teknis terpisah per *stage*, sekaligus membuat file aturan `skills.md`, `agent.md`, dan `work.md`.
* **Prompt 3 (Iterative Backend, ML & Raw-Frontend Execution):** Digunakan untuk mengeksekusi pembangunan sistem dari Stage 1 hingga Stage 6 dengan mematuhi aturan di `skills.md`, `agent.md`, dan `work.md`.

---

## PROMPT 1: Full Project Setup, Supabase Environment & MCP Configuration

*(Catatan: Pastikan Anda telah memasukkan file `credential.md` berisi kredensial Supabase ke dalam project sebelum atau saat menjalankan prompt ini).*

Salin dan berikan prompt di bawah ini kepada Antigravity pada sesi pertama:

```text
Kamu adalah Principal Full-Stack & DevOps Engineer. Kita akan membangun proyek bernama PATEN (Pemantauan Transaksi & Efektivitas Niaga) khusus untuk ekosistem Event & Evaluasi Bantuan UMKM di Kabupaten Kutai Timur.

Baca terlebih dahulu seluruh dokumen referensi proyek yang ada di workspace (BRD, PRD, ML Pipeline Configuration, Implementation Plan, serta file `credential.md` jika sudah tersedia).

Tugas kamu saat ini HANYA melakukan SETUP PENUH ENVIRONMENT, PROYEK, DATABASE CONNECTION, dan MCP (Model Context Protocol) dengan rincian berikut:

1. PEMINDAIAN & INISIALISASI PROYEK:
   - Periksa struktur direktori kerja saat ini.
   - Setup proyek menggunakan tech stack: Laravel 11/12 + Inertia.js (React atau Vue sesuai kesepakatan) dengan konfigurasi yang bersih.
   - Siapkan struktur folder modular untuk Backend (Services, Actions, Repositories/Models, Middleware), folder `/ml_pipeline` untuk menyimpan script Python/Colab sinkronisasi Machine Learning, serta folder `/docs` dan `/docs/staging` untuk dokumentasi pengerjaan.

2. KONFIGURASI ENVIRONMENT & KONEKSI SUPABASE DARI `credential.md`:
   - Baca kredensial koneksi database dan API Supabase dari file `credential.md`.
   - Siapkan konfigurasi `.env` dan `.env.example` untuk menghubungkan Laravel ke Supabase PostgreSQL (`DB_CONNECTION=pgsql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, serta variabel `SUPABASE_URL`, `SUPABASE_ANON_KEY`, dan `SUPABASE_SERVICE_ROLE_KEY`).
   - Pastikan ekstensi PostgreSQL `uuid-ossp` dan koneksi database dapat diuji menggunakan perintah artisan / script diagnostik.

3. SETUP MCP (MODEL CONTEXT PROTOCOL):
   - Konfigurasikan dan pastikan MCP server yang dibutuhkan aktif dan terhubung dengan baik (khususnya PostgreSQL / Supabase MCP untuk inspeksi skema dan query langsung sesuai `credential.md`, serta Filesystem/Git MCP bila tersedia).
   - Verifikasi apakah kamu dapat membaca status database melalui MCP dan koneksi CLI.

4. ATURAN KERJA (WORKFLOW):
   - Jangan menulis kode fitur bisnis terlebih dahulu pada tahap ini.
   - Jika file `credential.md` belum lengkap atau ada parameter yang perlu dikonfirmasi, tanyakan kepadaku terlebih dahulu.
   - Setelah koneksi Supabase dan environment terverifikasi sukses, buat file laporan singkat `docs/00_environment_and_mcp_setup.md` yang mendokumentasikan status kesiapan proyek.
```

---

## PROMPT 2: Technical Stage Breakdown & Generation of `skills.md`, `agent.md`, and `work.md`

Setelah Prompt 1 selesai dan koneksi Supabase serta MCP aktif, salin dan jalankan **Prompt 2** di bawah ini sebelum mulai menulis kode fitur:

```text
Kamu adalah Principal System Architect & Technical Lead untuk proyek PATEN (Pemantauan Transaksi & Efektivitas Niaga) Kabupaten Kutai Timur.

Environment proyek, koneksi Supabase, dan MCP sudah aktif. Sebelum kita masuk ke tahap penulisan kode fitur, tugas kamu saat ini adalah melakukan BREAKDOWN TEKNIS MENDALAM dari dokumen Implementation Plan & PRD, serta menyusun artefak dokumen kontrol kerja di dalam proyek.

Jangan menulis kode aplikasi pada tahap ini. Lakukan 2 tugas utama berikut secara berurutan:

===================================================================
TUGAS 1: MENYUSUN FILE DOKUMENTASI ATURAN INTI (`skills.md`, `agent.md`, `work.md`)
===================================================================
Buat 3 file Markdown berikut di root direktori proyek dengan isi yang tegas, terstruktur, dan mengikat:

1. `skills.md` (Alur Kerja / Standard Operating Procedure):
   Tuliskan aturan siklus pengerjaan wajib untuk setiap Stage:
   - STEP 1 (SCANNING): Melakukan pemindaian menyeluruh terhadap kondisi codebase saat ini, status tabel di Supabase via MCP, serta membaca file rencana teknis stage aktif (`docs/staging/stage_X_plan.md`).
   - STEP 2 (ASK IF NEEDED / Q&A GATE): Mengajukan pertanyaan klarifikasi kepada user apabila ditemukan ambiguitas logika bisnis, edge-case keamanan, atau kebutuhan konfirmasi parameter dari hasil scanning, lalu menunggu persetujuan sebelum mengeksekusi kode.
   - STEP 3 (IMPLEMENT): Mengeksekusi implementasi kode backend, database, integrasi ML, dan raw-frontend sesuai rencana teknis yang telah disepakati, diikuti dengan pengujian otomatis/fungsional.
   - STEP 4 (WRITE DOCS):
     a. Membuat dokumentasi hasil implementasi terpisah untuk setiap stage di `docs/stage_{nomor}_implementation.md`.
     b. Menambahkan (append) hasil pengujian ke dalam SATU file dokumentasi testing terpusat bernama `docs/CONSOLIDATED_TESTING_REPORT.md` (dari Stage 1 sampai Stage terakhir wajib tergabung dalam 1 file ini).
   - STEP 5 (REPEAT ON NEXT STAGE): Setelah seluruh test pada stage aktif berstatus PASS dan dokumentasi selesai, pindah ke target stage berikutnya dan ulangi siklus mulai dari STEP 1 (SCANNING).

2. `agent.md` (Aturan Penggunaan MCP & Kredensial Database):
   Tuliskan aturan operasional agen yang wajib dipatuhi setiap saat:
   - WAJIB selalu menggunakan MCP (Model Context Protocol) yang telah di-setup untuk setiap operasi inspeksi database, eksekusi DDL/query PostgreSQL Supabase, dan manajemen file proyek.
   - WAJIB selalu mengacu pada file `credential.md` sebagai satu-satunya sumber kebenaran (*single source of truth*) untuk kredensial koneksi database Supabase, API keys, dan konfigurasi environment. Jika terjadi kegagalan koneksi, agen harus memeriksa kesesuaian `.env` terhadap `credential.md`.
   - Dilarang mengubah kredensial koneksi atau menggunakan database lokal (seperti SQLite in-memory) yang menyimpang dari konfigurasi Supabase pada `credential.md`.

3. `work.md` (Fokus Implementasi Backend, Wajib Testing PASS, & Larangan Styling Frontend):
   Tuliskan batasan teknis pengerjaan proyek:
   - FOKUS UTAMA: Prioritaskan 100% ketepatan logika Backend, Arsitektur API, Keamanan Endpoint/Token, Transaksi Database Atomik, serta Integrasi SPK & Machine Learning sesuai regulasi Perbup UMKM Kutai Timur.
   - KEWAJIBAN TESTING PER STAGE: Setiap selesai mengimplementasikan fitur pada suatu stage, agen WAJIB membuat dan menjalankan pengujian (Feature Test, Unit Test, Database Assertion, & Security Check). Pengerjaan sebuah stage TIDAK BOLEH dinyatakan selesai sebelum seluruh pengujian mencapai status 100% BERHASIL (`PASS`).
   - LARANGAN MUTLAK STYLING FRONTEND (RAW FRONTEND ONLY):
     * DILARANG KERAS mendesain, mempercantik, atau menambahkan styling CSS/Tailwind estetika dalam bentuk apa pun pada halaman Frontend Inertia.js.
     * Halaman Frontend HANYA BOLEH berisi elemen HTML fungsional murni tanpa kosmetik, yaitu: `<form>`, `<input>`, `<select>` (dropdown), `<textarea>`, `<button>`, `<table>`, dan elemen teks dasar yang berfokus murni pada AKSI pengguna, pengiriman payload, serta pengujian alur data dari Frontend ke Backend.
     * Untuk fitur konfigurasi warna (`mono`/`duo`) dan export poster/menu, fokuskan pada penyimpanan data konfigurasi di database, injeksi nilai variabel mentah, serta keberhasilan endpoint men-generate output fungsional (QR code, payload, dan struktur data).

===================================================================
TUGAS 2: BREAKDOWN RENCANA TEKNIS PER STAGE (STAGING ARTIFACT PLANS)
===================================================================
Baca kembali `implementation_plan_paten.md`, `prd_paten_kutim.md`, dan `ml_pipeline_colab_paten.md`. Terjemahkan rencana tersebut ke ranah teknis tingkat lanjut dengan membuat 6 file Markdown terpisah di dalam folder `docs/staging/`:

1. `docs/staging/stage_1_plan.md` (Core Infrastructure, Supabase DDL Migrations, Models & Security Middleware)
2. `docs/staging/stage_2_plan.md` (Modul 1: Form-to-Pages Event Generator, Lock Confirmation, Color Config, Poster QR Export & Public Attendance Gamification)
3. `docs/staging/stage_3_plan.md` (Modul 2: CMS Hidden URL Token Auth, Rundown Extender CRUD, Stand & Facility Provisioning, Gate Check-in & Doorprize Picker)
4. `docs/staging/stage_4_plan.md` (Modul 3: Assign Stand UMKM Perbup Attributes, Menu CRUD & Re-Export, Zero-Friction Dropdown POS Mini)
5. `docs/staging/stage_5_plan.md` (Modul 4: Hybrid SPK Engine, Perbup Guardrail 1 KK 1 Penerima, Google Colab ML Sync & Awarding Leaderboard Top 1/3/5)
6. `docs/staging/stage_6_plan.md` (End-to-End Integration, Atomic Concurrency Testing, Data Leak Audit & Final Verification)

Setiap file `stage_X_plan.md` WAJIB merinci secara teknis:
- Daftar tabel/kolom database dan relasi model yang terlibat.
- Daftar Route/Endpoint API & Inertia beserta HTTP Method, Controller/Service, dan Middleware keamanannya.
- Spesifikasi Request Payload, Aturan Validasi, dan struktur Response.
- Daftar elemen aksi Raw Frontend (input, dropdown, button) yang akan dibuat pada halaman Inertia untuk menguji fitur tersebut.
- Daftar skenario pengujian otomatis (Test Cases) yang harus berstatus PASS pada stage tersebut.

Setelah seluruh file (`skills.md`, `agent.md`, `work.md`, dan keenam file `docs/staging/stage_X_plan.md`) selesai dibuat, tampilkan ringkasan singkatnya kepadaku dan tunggu instruksi untuk memulai eksekusi Stage 1.
```

---

## PROMPT 3: Iterative Backend, Security, ML & Raw-Frontend Execution Prompt

Setelah Prompt 2 selesai menghasilkan `skills.md`, `agent.md`, `work.md`, dan seluruh file rencana teknis di `docs/staging/`, gunakan **Prompt 3** di bawah ini untuk memulai eksekusi pembangunan kode dari Stage 1 sampai Stage 6:

```text
Kamu adalah Lead Backend, Security & Machine Learning Engineer untuk proyek PATEN (Pemantauan Transaksi & Efektivitas Niaga) Kabupaten Kutai Timur.

Sebelum melakukan tindakan apa pun, BACA dan PATUHI SEPENUHNYA ketiga file aturan inti di root proyek:
1. `skills.md` (Alur kerja wajib: Scan -> Ask if needed -> Implement -> Write Docs -> Repeat on next stage)
2. `agent.md` (Kewajiban menggunakan MCP dan koneksi database Supabase sesuai `credential.md`)
3. `work.md` (Fokus murni Backend/Security/ML, wajib testing PASS di setiap stage, dan LARANGAN MUTLAK melakukan styling frontend—hanya gunakan elemen input, dropdown, button, dan tabel mentah untuk kebutuhan aksi aplikasi)

ATURAN EKSEKUSI PER STAGE:
Kita akan mengerjakan proyek ini secara berurutan dari Stage 1 hingga Stage 6 mengacu pada file rencana teknis di `docs/staging/stage_1_plan.md` s/d `docs/staging/stage_6_plan.md`.

Untuk SETIAP STAGE, jalankan siklus dari `skills.md` dengan disiplin penuh:
1. SCANNING: Periksa kondisi codebase, cek database Supabase via MCP (sesuai `agent.md` & `credential.md`), dan baca `docs/staging/stage_{aktif}_plan.md`.
2. ASK IF NEEDED (Q&A GATE): Laporkan hasil scanning, paparkan rencana eksekusi, dan ajukan pertanyaan konfirmasi jika ada hal yang perlu dipastikan. BERHENTI dan TUNGGU jawabanku sebelum menulis kode implementasi.
3. IMPLEMENT & TEST: Setelah aku menyetujui, implementasikan seluruh komponen Backend, Database, ML/SPK, serta elemen aksi Raw Frontend (tanpa styling sesuai `work.md`). Jalankan pengujian hingga 100% test mencapai status PASS.
4. WRITE DOCS:
   - Buat laporan implementasi stage di `docs/stage_{nomor}_implementation.md`.
   - Tambahkan (append) hasil lengkap pengujian stage tersebut ke dalam file tunggal `docs/CONSOLIDATED_TESTING_REPORT.md`.
5. NEXT STAGE: Setelah semua test PASS dan dokumentasi selesai, beri tahu aku dan lanjutkan ke stage berikutnya mulai kembali dari langkah SCANNING.

Sekarang, mulailah untuk **STAGE 1** dengan membaca `docs/staging/stage_1_plan.md` serta menjalankan **STEP 1 (SCANNING)** dan **STEP 2 (ASK IF NEEDED)**.
```