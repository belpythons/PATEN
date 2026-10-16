# Batasan Teknis & Fokus Pengerjaan – Proyek PATEN

Dokumen ini memuat batas-batas rekayasa teknis, prioritas implementasi, standar kelulusan pengujian, serta aturan ketat terkait antarmuka pengguna dalam pengembangan sistem **PATEN (Pemantauan Transaksi & Efektivitas Niaga)** Kabupaten Kutai Timur.

---

## 1. Fokus Utama: Backend, Keamanan, & Integrasi SPK/ML

Pengerjaan proyek PATEN mengutamakan ketahanan fungsional dan integritas data tingkat enterprise:
1. **Ketepatan Logika Bisnis & Kepatuhan Regulasi:**
   - Implementasi penuh aturan kepatuhan Perbup Kutim: validasi 1 KK = 1 penerima hibah, verifikasi masa aktif NIB $\ge 6$ bulan, domisili Kutim, status non-ASN/TNI/Polri, dan pencegahan duplikasi bantuan.
   - Perhitungan matematis SPK menggunakan formulasi *Simple Additive Weighting (SAW)* dengan pembobotan fitur transaksi POS yang objektif dan transparan.
2. **Arsitektur API & Transaksi Atomik:**
   - Desain RESTful API yang bersih, efisien, dan dilengkapi validasi input yang ketat (*Form Request Validation*).
   - Penggunaan transaksi database (`DB::transaction`) dengan penanganan konkurensi (optimistic/pessimistic locking) pada endpoint pencatatan transaksi POS Mini guna menjamin konsistensi mutlak antara log transaksi, agregasi omzet, dan poin keaktifan.
3. **Keamanan Endpoint & Otorisasi:**
   - Pengamanan akses Hidden URL CMS Penyelenggara via `VerifyCmsTokenMiddleware` (validasi 64-karakter cryptographic token).
   - Pengamanan akses terminal POS Mini via `VerifyPosPinMiddleware` (validasi PIN 6-digit per nomor stand).
   - Penerapan proteksi *Rate Limiting* pada rute publik untuk mencegah brute-force dan spam pendaftaran doorprize/tiket.

---

## 2. Kewajiban Testing 100% PASS Per Stage

1. **Kelengkapan Jenis Pengujian:**
   - Setiap stage wajib menyertakan pengujian komprehensif yang mencakup:
     * **Feature Test:** Memverifikasi fungsionalitas rute HTTP, kode status respons (200, 201, 401, 403, 422), dan payload JSON.
     * **Unit Test:** Memverifikasi kalkulasi formula SPK, normalisasi min-max, dan utilitas enkripsi/hashing.
     * **Database Assertion:** Memverifikasi keberadaan, pembaruan, dan integritas relasi record di database Supabase PostgreSQL.
     * **Security Audit Test:** Memverifikasi penolakan akses token/PIN yang tidak valid dan pencegahan kebocoran data sensitif (seperti NIK, No KK, dan nominal omzet pada respons publik).
2. **Kriteria Kelulusan Mutlak:**
   - Pengerjaan suatu stage **TIDAK BOLEH** dinyatakan selesai dan **DILARANG** berpindah ke stage berikutnya sebelum seluruh skenario pengujian yang dirancang berstatus **100% PASS** tanpa kegagalan (*zero failure*).
   - Seluruh hasil pengujian wajib ditambahkan ke `docs/CONSOLIDATED_TESTING_REPORT.md`.

---

## 3. Larangan Mutlak Styling Frontend (Raw Frontend Only)

Prinsip dasar frontend pada tahap ini adalah **Pure Functional Verification**:

1. **Larangan Kosmetik & Estetika:**
   - **DILARANG KERAS** menambahkan styling visual, CSS utility (TailwindCSS classes estetika seperti warna-warni, shadow, rounded cards, transition efek, dsb.), atau CSS custom dengan tujuan mempercantik tampilan.
   - Seluruh komponen Vue 3 (`.vue`) pada Inertia.js harus berupa markup HTML mentah (*barebones HTML*) yang berfokus murni pada ketersediaan input dan aksi.
2. **Elemen yang Diperbolehkan:**
   - Halaman frontend HANYA BOLEH berisi elemen-elemen fungsional standar:
     * `<form>` untuk pembungkus pengiriman data.
     * `<input type="text|number|date|color|hidden">` untuk entri nilai.
     * `<select>` dan `<option>` untuk pemilihan dropdown (seperti pilihan menu stand dan metode bayar).
     * `<textarea>` untuk deskripsi dan alamat.
     * `<button type="submit|button">` untuk memicu aksi.
     * `<table>`, `<tr>`, `<th>`, `<td>` untuk inspeksi tabulasi data dan leaderboard.
     * Tag teks dasar (`<h1>`, `<h2>`, `<p>`, `<span>`, `<div>`) murni untuk pelabelan form dan pesan status/error.
3. **Penanganan Fitur Desain & Export (Warna & Poster):**
   - Untuk fitur pemilihan skema warna (`mono`/`duo`), fokuskan pada validasi penyimpanan kode Hex di kolom database dan penyuntikan nilai variabel mentah ke atribut/payload.
   - Untuk fitur export poster dan daftar menu, fokuskan pada fungsionalitas endpoint backend dalam menghasilkan data QR Code, tautan publik, serta struktur data output yang dapat diunduh/dilihat.
