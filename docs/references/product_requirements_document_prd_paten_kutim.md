# Product Requirements Document (PRD)
**Nama Produk:** PATEN (Pemantauan Transaksi & Efektivitas Niaga)  
**Tech Stack:** Laravel 11/12 (Backend & API) + Inertia.js (Frontend State/Routing) + Supabase (PostgreSQL Database & Realtime) + Python (ML Pipeline Service).

---

## 1. Gambaran Umum Sistem & Arsitektur Modul

Aplikasi PATEN terdiri dari **4 Modul Utama** yang saling terhubung:
1. **Modul 1: Form-to-Pages Event Generator, Custom Design System & Gamifikasi Publik**
2. **Modul 2: CMS Hidden URL / Admin Console Penyelenggara**
3. **Modul 3: Assign Stand UMKM, Menu Designer & Lightweight POS Mini**
4. **Modul 4: SPK Terintegrasi Machine Learning (Khusus Evaluasi Program Bantuan Kutim)**

---

## 2. Spesifikasi Fungsional Per Modul

### Modul 1: Form-to-Pages Event Generator & Gamifikasi Publik
* **FR-1.1 Form Pembuatan Event:** Penyelenggara mengisi form tunggal yang memuat: Nama Event, Deskripsi, Instansi Penyelenggara, Daftar Sponsor, Lokasi Venue, Tanggal & Waktu Mulai–Selesai, serta Rundown Awal.
* **FR-1.2 Konfirmasi Pengisian:** Sebelum men-generate halaman, sistem menampilkan modal konfirmasi untuk memastikan data inti (seperti URL slug dan nama instansi) sudah benar.
* **FR-1.3 Custom Design System (Pop-Quiz Theme Selector):**
  * Pengguna memilih preset tata letak template.
  * Pengguna memilih skema warna: **Opsi 1 Warna (`Mono` - Primary Hex)** atau **Opsi 2 Warna (`Duo` - Primary & Secondary Hex)**.
  * Sistem menyuntikkan CSS Variables (`--theme-primary`, `--theme-secondary`) ke halaman publik event dan desain cetak.
* **FR-1.4 Opsi Gamifikasi Fleksibel (Dapat diaktifkan saat awal form atau di CMS):**
  * **Mode A - Tiket HTM:** Pencatatan pembelian/pendaftaran tiket perorangan lengkap dengan kode QR unik untuk pemindaian kehadiran di pintu masuk.
  * **Mode B - Absensi Kupon Doorprize (Event Gratis):** Formulir pendaftaran pengunjung di halaman event (`Nama Lengkap`, `Nomor WA`, `Kecamatan`) untuk mendapatkan nomor undian doorprize sekaligus mendata jumlah kehadiran riil warga.
  * **Mode C - Awarding UMKM Terlaris:** Klasemen keaktifan stand (Top 1 / Top 3 / Top 5 sesuai pengaturan penyelenggara). Perhitungan didasarkan pada akumulasi pencatatan transaksi di POS, namun **nominal rupiah disembunyikan dari publik** (hanya menampilkan peringkat/poin keaktifan).
* **FR-1.5 Export Poster Event Otomatis:** Sistem menghasilkan file poster siap sebar yang memuat informasi event, warna tema terpilih, serta **QR Code / Barcode dinamis** yang mengarah langsung ke URL halaman publik event (`/e/{slug}`).

### Modul 2: CMS Hidden URL / Admin Penyelenggara
* **FR-2.1 Akses Tanpa Login Rumit (Hidden URL Token):** Setelah event di-generate, penyelenggara mendapatkan URL khusus ber-token keamanan tinggi (`/manage/event/{id}?token={secret_token}`).
* **FR-2.2 CRUD & Extend Rundown Kegiatan:** Penyelenggara dapat menambah, mengedit waktu, atau memperpanjang jadwal/rundown kegiatan sewaktu-waktu apabila ada perubahan di lapangan.
* **FR-2.3 Setup Stand & Penyesuaian Fasilitas:** Penyelenggara mengatur jumlah kuota stand (misal: Stand 01 s/d Stand 50) beserta detail fasilitas yang diberikan per stand (daya listrik dalam Watt, jumlah meja/kursi, ketersediaan air bersih, dan ukuran tenda).
* **FR-2.4 Dashboard Evaluasi Keramaian & Awarding:** Menampilkan rekapitulasi real-time jumlah tiket HTM ter-scan, daftar peserta absensi doorprize (lengkap dengan fitur acak pemenang undian), peringkat pemenang Awarding UMKM, serta total agregat perputaran uang seluruh event.

### Modul 3: Assign Stand UMKM, Menu Designer & POS Mini
* **FR-3.1 Form Biodata & Assign Stand UMKM:** Pendaftaran UMKM ke nomor stand tertentu dengan menginput:
  * Identitas Kepatuhan Perbup: `NIK`, `Nomor KK` (validasi 1 KK 1 penerima), `Nama Pemilik`, `Nama Usaha`, `Nomor WA`, `Kecamatan Domisili`, `Status NIB` (beserta lama aktif dalam bulan), `Status Halal/PIRT/HKI`, dan `Riwayat Penerimaan Hibah`.
  * Input daftar menu awal (Nama Produk/Menu & Harga Satuan).
* **FR-3.2 Banner Edukasi Program Bantuan UMKM:** Menampilkan informasi resmi yang jelas di halaman pendaftaran dan POS bahwa pencatatan penjualan ini merupakan syarat portofolio keaktifan untuk seleksi program bantuan UMKM Kutai Timur (bukan untuk pemungutan pajak).
* **FR-3.3 Export Desain Menu Penjualan (Re-exportable):** Sistem men-generate lembar daftar menu siap cetak/unduh yang mengikuti fleksibilitas warna desain event (`Primary` / `Secondary`). Pedagang dapat menambah menu baru atau merevisi harga kapan saja, lalu melakukan *export ulang* daftar menu secara instan.
* **FR-3.4 POS Mini Super Simpel:**
  * Akses cepat per stand menggunakan kode PIN 6 digit.
  * Antarmuka kasir hanya terdiri dari: **Dropdown Pilihan Menu (otomatis memuat harga)**, **Input Kuantitas (`Qty`)**, dan **Pilihan Metode Pembayaran (`Tunai` / `QRIS`)** lalu tombol **Simpan Transaksi**.

### Modul 4: SPK Terintegrasi Machine Learning (CMS Pengelola)
* **FR-4.1 Filter Regulasi Perbup (Rule-Based Guardrail):** Memvalidasi syarat mutlak sebelum penilaian hibah modal:
  * Domisili Kutim = `True`
  * Bukan ASN/TNI/Polri = `True`
  * Cek Duplikasi `no_kk` = Hanya 1 usaha per KK yang diizinkan masuk nominasi Hibah Modal APBD.
  * NIB Aktif $\ge 6\text{ bulan}$ dan `pernah_terima_hibah_apbd == False`.
* **FR-4.2 Kalkulasi Skor SPK & Klasifikasi ML:** Mengolah data transaksi POS selama event berlangsung (total frekuensi transaksi, konsistensi jam operasional, rasio transaksi QRIS, dan rata-rata nilai transaksi) menggunakan pipeline Machine Learning untuk mengelompokkan UMKM dan memberikan rekomendasi intervensi:
  1. `Fasilitasi Legalitas (NIB/Halal/PIRT/HKI)`
  2. `Bantuan Modal Awal BAZNAS / TKM Kemnaker (Rp 5 Juta)`
  3. `Hibah Peralatan Produksi (Pemkab / CSR)`
  4. `Hibah Permodalan APBD Kutim (Rp 5 - 25 Juta) [Kuota Prioritas 200 UMKM]`
  5. `Kredit Lunak BPR Kutim / KUR Bankaltimtara & Inkubasi CSR KPC`

---

## 3. Formulasi Matematika SPK & Scoring Engine

Untuk menjaga transparansi di CMS Pengelola, skor akhir keaktifan UMKM ($S_{\text{total}}$) dihitung menggunakan pembobotan fitur ternormalisasi (Simple Additive Weighting / Feature Weighted Score) yang dikombinasikan dengan hasil klasterisasi Machine Learning:

$$S_{\text{total}} = (w_1 \cdot \hat{F}_{\text{freq}}) + (w_2 \cdot \hat{F}_{\text{omzet}}) + (w_3 \cdot \hat{F}_{\text{konsistensi}}) + (w_4 \cdot \hat{F}_{\text{qris}}) + (w_5 \cdot \hat{F}_{\text{legalitas}})$$

Di mana bobot ditentukan sebesar:
* $w_1 = 0.30$ (Frekuensi transaksi terlayani $\hat{F}_{\text{freq}}$, mencegah manipulasi input 1 transaksi bernominal besar palsu)
* $w_2 = 0.20$ (Total omzet terekam ternormalisasi $\hat{F}_{\text{omzet}}$)
* $w_3 = 0.25$ (Konsistensi jam/hari aktif bertransaksi selama durasi event $\hat{F}_{\text{konsistensi}}$)
* $w_4 = 0.10$ (Rasio adopsi pembayaran non-tunai/QRIS $\hat{F}_{\text{qris}}$)
* $w_5 = 0.15$ (Kelengkapan legalitas usaha $\hat{F}_{\text{legalitas}}$)

Normalisasi Min-Max untuk setiap metrik $x_i$ pada suatu event:

$$\hat{F}_i = \frac{x_i - \min(x)}{\max(x) - \min(x) + \epsilon}$$

dengan $\epsilon = 10^{-9}$ untuk menghindari pembagian dengan nol.

---

## 4. Skema Database Lengkap (Supabase PostgreSQL DDL)

```sql
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ENUMS
CREATE TYPE color_scheme_mode AS ENUM ('mono', 'duo');
CREATE TYPE gamification_attendance_mode AS ENUM ('none', 'htm', 'doorprize', 'both');
CREATE TYPE payment_method_enum AS ENUM ('cash', 'qris');
CREATE TYPE awarding_tier_mode AS ENUM ('none', 'top_1', 'top_3', 'top_5');
CREATE TYPE program_recommendation_enum AS ENUM (
    'unverified',
    'fasilitasi_legalitas_nib_halal',
    'bantuan_baznas_tkm_5jt',
    'hibah_peralatan_produksi',
    'hibah_modal_apbd_5_25jt',
    'kredit_bpr_bankaltimtara_csr'
);

-- 1. TABEL EVENTS
CREATE TABLE events (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    slug VARCHAR(120) UNIQUE NOT NULL,
    nama_event VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    penyelenggara VARCHAR(150) NOT NULL,
    daftar_sponsor JSONB DEFAULT '[]'::JSONB,
    lokasi_venue VARCHAR(255) NOT NULL,
    tanggal_mulai TIMESTAMP WITH TIME ZONE NOT NULL,
    tanggal_selesai TIMESTAMP WITH TIME ZONE NOT NULL,
    
    -- Hidden CMS Access Token
    cms_secret_token VARCHAR(64) UNIQUE NOT NULL,
    
    -- Custom Design System
    template_preset VARCHAR(50) DEFAULT 'kutim_formal',
    color_mode color_scheme_mode DEFAULT 'duo',
    primary_color VARCHAR(7) DEFAULT '#0F766E',
    secondary_color VARCHAR(7) DEFAULT '#F59E0B',
    poster_url TEXT,
    
    -- Gamification Config
    attendance_mode gamification_attendance_mode DEFAULT 'doorprize',
    harga_htm NUMERIC(12, 2) DEFAULT 0.00,
    awarding_mode awarding_tier_mode DEFAULT 'top_3',
    is_locked_confirmed BOOLEAN DEFAULT TRUE,
    
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- 2. TABEL RUNDOWN KEGIATAN
CREATE TABLE event_rundowns (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    event_id UUID NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    judul_kegiatan VARCHAR(200) NOT NULL,
    deskripsi_kegiatan TEXT,
    waktu_mulai TIMESTAMP WITH TIME ZONE NOT NULL,
    waktu_selesai TIMESTAMP WITH TIME ZONE NOT NULL,
    pengisi_acara VARCHAR(150),
    urutan INT DEFAULT 1,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- 3. TABEL PROFIL UMKM (TERINTEGRASI REGULASI PERBUP KUTIM)
CREATE TABLE umkm_profiles (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    nik VARCHAR(16) UNIQUE NOT NULL,
    no_kk VARCHAR(16) NOT NULL,
    nama_pemilik VARCHAR(150) NOT NULL,
    nama_usaha VARCHAR(150) NOT NULL,
    nomor_wa VARCHAR(20) NOT NULL,
    kecamatan VARCHAR(100) NOT NULL,
    alamat_lengkap TEXT,
    kategori_usaha VARCHAR(50) NOT NULL, -- Kuliner, Kriya, Fesyen, Agribisnis, Jasa
    
    -- Parameter Regulasi Perbup Kutim
    is_domisili_kutim BOOLEAN DEFAULT TRUE,
    is_bukan_asn_tni_polri BOOLEAN DEFAULT TRUE,
    nomor_nib VARCHAR(50),
    lama_nib_bulan INT DEFAULT 0, -- Syarat hibah modal >= 6 bulan
    has_npwp BOOLEAN DEFAULT FALSE,
    has_sku_kecamatan BOOLEAN DEFAULT FALSE,
    has_sertifikat_halal BOOLEAN DEFAULT FALSE,
    has_pirt BOOLEAN DEFAULT FALSE,
    has_hki BOOLEAN DEFAULT FALSE,
    pernah_terima_hibah_apbd BOOLEAN DEFAULT FALSE,
    tahun_terima_hibah INT,
    
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- 4. TABEL STAND EVENT & FASILITAS
CREATE TABLE event_stands (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    event_id UUID NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    umkm_id UUID REFERENCES umkm_profiles(id) ON DELETE SET NULL,
    kode_stand VARCHAR(20) NOT NULL,
    fasilitas JSONB DEFAULT '{"listrik_watt": 450, "meja": 1, "kursi": 2, "tenda": "Sarnafil 3x3", "air_bersih": false}'::JSONB,
    pin_akses_pos VARCHAR(6) NOT NULL,
    menu_poster_url TEXT,
    
    -- Metrik Agregat Realtime
    total_transaksi INT DEFAULT 0,
    total_omzet NUMERIC(14, 2) DEFAULT 0.00,
    poin_keaktifan INT DEFAULT 0, -- Ditampilkan ke publik untuk Awarding
    
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    CONSTRAINT uq_event_kode_stand UNIQUE (event_id, kode_stand)
);

-- 5. TABEL MENU STAND UMKM
CREATE TABLE stand_menus (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    stand_id UUID NOT NULL REFERENCES event_stands(id) ON DELETE CASCADE,
    nama_menu VARCHAR(150) NOT NULL,
    harga NUMERIC(12, 2) NOT NULL,
    is_aktif BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- 6. TABEL TRANSAKSI POS MINI
CREATE TABLE pos_transactions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    event_id UUID NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    stand_id UUID NOT NULL REFERENCES event_stands(id) ON DELETE CASCADE,
    menu_id UUID NOT NULL REFERENCES stand_menus(id) ON DELETE RESTRICT,
    nama_menu_snapshot VARCHAR(150) NOT NULL,
    harga_satuan_snapshot NUMERIC(12, 2) NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    total_harga NUMERIC(12, 2) NOT NULL,
    metode_pembayaran payment_method_enum NOT NULL DEFAULT 'cash',
    recorded_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- 7. TABEL KEHADIRAN PENGUNJUNG (HTM & DOORPRIZE)
CREATE TABLE event_attendees (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    event_id UUID NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    nama_pengunjung VARCHAR(150) NOT NULL,
    nomor_wa VARCHAR(20) NOT NULL,
    kecamatan_asal VARCHAR(100),
    tipe_tiket gamification_attendance_mode NOT NULL, -- 'htm' atau 'doorprize'
    kode_tiket_kupon VARCHAR(40) UNIQUE NOT NULL,
    nominal_htm_terbayar NUMERIC(10, 2) DEFAULT 0.00,
    is_hadir_checked_in BOOLEAN DEFAULT FALSE,
    waktu_check_in TIMESTAMP WITH TIME ZONE,
    is_pemenang_doorprize BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- 8. TABEL EVALUASI SPK & REKOMENDASI MACHINE LEARNING
CREATE TABLE spk_umkm_evaluations (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    event_id UUID NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    stand_id UUID NOT NULL REFERENCES event_stands(id) ON DELETE CASCADE,
    umkm_id UUID NOT NULL REFERENCES umkm_profiles(id) ON DELETE CASCADE,
    
    -- Status Kepatuhan Regulasi Perbup
    is_lolos_syarat_perbup BOOLEAN DEFAULT FALSE,
    alasan_gagal_perbup TEXT,
    
    -- Fitur Numerik Input ML
    frekuensi_transaksi INT DEFAULT 0,
    total_omzet_event NUMERIC(14, 2) DEFAULT 0.00,
    rasio_transaksi_qris NUMERIC(5, 4) DEFAULT 0.0000,
    rasio_jam_aktif NUMERIC(5, 4) DEFAULT 0.0000,
    skor_kelengkapan_legalitas NUMERIC(5, 2) DEFAULT 0.00,
    
    -- Output ML & SPK
    ml_cluster_label VARCHAR(50),
    skor_akhir_spk NUMERIC(6, 2) DEFAULT 0.00,
    rekomendasi_program program_recommendation_enum DEFAULT 'unverified',
    peringkat_prioritas_event INT,
    
    evaluated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    CONSTRAINT uq_evaluasi_stand UNIQUE (event_id, stand_id)
);
```