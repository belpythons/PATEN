# Aturan Operasional Agen & Tata Kelola MCP – Proyek PATEN

Dokumen ini berisi batasan, kewajiban, dan protokol operasional mutlak yang mengikat asisten AI (Antigravity Agent) selama proses perancangan, implementasi, dan pengujian sistem **PATEN (Pemantauan Transaksi & Efektivitas Niaga)** Kabupaten Kutai Timur.

---

## 1. Kewajiban Penggunaan MCP (Model Context Protocol)

1. **Inspeksi & Manipulasi Database:**
   - Agen WAJIB memanfaatkan server MCP PostgreSQL (`supabase-postgres`) yang telah dikonfigurasi untuk memeriksa struktur tabel, foreign key, tipe data, indeks, dan eksekusi query verifikasi langsung pada Supabase PostgreSQL.
   - Dilarang membuat asumsi skema tabel tanpa melakukan verifikasi terlebih dahulu ke database aktif melalui MCP atau koneksi PDO/Artisan resmi.
2. **Manajemen Berkas Proyek:**
   - Agen WAJIB memanfaatkan alat manipulasi berkas (MCP Filesystem atau built-in file tools) untuk mengelola struktur proyek dengan rapi, menjaga keutuhan file yang ada, dan memastikan tidak ada berkas penting yang terhapus secara tidak disengaja.

---

## 2. Kredensial Database: Single Source of Truth

1. **Kepatuhan Mutlak Terhadap `credential.md`:**
   - Berkas `docs/references/credential.md` adalah **satu-satunya sumber kebenaran (*single source of truth*)** untuk seluruh konfigurasi database Supabase PostgreSQL, API URL, Anon Key, dan Service Role Key.
   - Konfigurasi pada berkas `.env` dan `.env.example` wajib selalu sinkron dan setia terhadap isi `docs/references/credential.md`.
2. **Penanganan Masalah Konektivitas:**
   - Apabila terjadi gangguan koneksi database (misalnya kegagalan resolusi host, batas koneksi pooler, atau authentication failure), agen WAJIB melakukan audit silang antara `.env` dan `docs/references/credential.md`.
   - Agen dilarang merevisi kredensial secara sepihak atau mengganti host/port tanpa konfirmasi pengguna.

---

## 3. Larangan Penggunaan Database Lokal (No Local SQLite Fallback)

1. **Larangan Mutlak SQLite In-Memory:**
   - Agen DILARANG KERAS mengalihkan koneksi database pengujian ke database lokal seperti SQLite (`:memory:` atau berkas `database.sqlite`) yang menyimpang dari arsitektur PostgreSQL Supabase.
   - Seluruh pengujian fitur (`Feature Test`, `Integration Test`, dan `Unit Test`) wajib dieksekusi langsung terhadap Supabase PostgreSQL untuk memvalidasi:
     * Perilaku tipe data native PostgreSQL (UUID, JSONB, ENUM, TIMESTAMP WITH TIME ZONE).
     * Integritas referensial dan constraint (`ON DELETE CASCADE`, `ON DELETE SET NULL`, `UNIQUE`).
     * Fungsi ekstensi PostgreSQL seperti `uuid_generate_v4()` dari `uuid-ossp` dan `gen_random_uuid()`.
     * Transaksi database atomik (`DB::transaction`) dan perilaku concurrency locking.
2. **Integritas Konfigurasi Pengujian:**
   - File `phpunit.xml` dilarang mendefinisikan override `<env name="DB_CONNECTION" value="sqlite"/>` yang menimpa konfigurasi Supabase pada `.env`.
