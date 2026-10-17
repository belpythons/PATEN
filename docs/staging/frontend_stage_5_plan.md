# Frontend Technical Staging Plan: STAGE 5 (Cobalt Edition)
**Nama Tahapan:** Modul 3 – UMKM Perbup Profiling, Touchscreen Pinpad, Graphite POS Mini ($\le 3$s) & Technical Menu Sheet  
**Target Modul:** Pendaftaran Stand UMKM (`/stands/{id}/assign`), Keypad Sentuh Login Kasir (`/pos/{id}/login`), Terminal Kasir POS Mini Super Cepat ($\le 3$s) (`/pos/{id}`), dan Lembar Menu Meja Siap Cetak (`/pos/{id}/export-menu`)  
**Standar Hallmark:** Banner Edukasi Anti-Pajak Resmi, Kartu Kasir Graphite Gelap Presisi, Target Waktu $\le 3$ Detik (BRD), dan Tombol Taktil 6px  

---

## 1. Analisis Kebutuhan & Spesifikasi Desain Cobalt

### A. Pendaftaran Stand & Profiling Perbup Kutim (`Pages/Stands/Assign.vue` - FR-3.1 & FR-3.2)
Pedagang UMKM membuka link pendaftaran stand via WhatsApp:
- **Official Government Notice Banner (FR-3.2):**
  - Banner berbingkai hairline 1px di bagian atas dengan suara resmi yang menenangkan:
    *"Pemberitahuan Resmi Dinas Koperasi & UKM Kabupaten Kutai Timur: Pencatatan penjualan harian ini merupakan syarat portofolio keaktifan untuk seleksi program bantuan UMKM Kutai Timur (Hibah Modal APBD, BAZNAS, Bantuan Alat Usaha). Sistem ini BUKAN untuk dasar penarikan pajak daerah atau penambahan biaya sewa stand."*
  - Dilengkapi lencana identitas Pemkab Kutai Timur dalam tipografi `Space Grotesk`.
- **Formulir Kepatuhan Perbup Bergaris Rambut:**
  - *Identitas & KK:* NIK (16 digit), Nomor KK (16 digit untuk verifikasi aturan 1 KK 1 Penerima), Nama Pemilik, Nama Usaha, Nomor WhatsApp, Kecamatan Domisili (18 Kecamatan Kutim), Alamat Lengkap.
  - *Parameter Legalitas:* Nomor NIB & usia NIB, checklist sertifikasi (Halal, P-IRT, HKI).
  - *Riwayat Hibah:* Status penerimaan hibah APBD sebelumnya & tahun penerimaan.
  - *Katalog Menu Awal:* Input produk pertama dan harga satuan.

### B. Touchscreen Keypad Login Kasir (`Pages/Pos/Login.vue` - FR-3.4)
- Kasir stan bazar membuka URL terminal kasir di ponsel:
  - **Papan Tombol Angka Sentuh (Touch Numpad 0-9):** Tombol besar berdimensi $\ge 56\text{px}$ dengan radius 6px yang nyaman ditekan di layar sentuh ponsel.
  - **6 Indikator Titik PIN:** Titik visual (*PIN dots*) yang terisi secara mulus saat angka ditekan.
  - **Auto-Submit Ergonomics:** Form otomatis mengirim verifikasi saat digit ke-6 ditekan tanpa memerlukan ketukan tombol tambahan.
  - Tombol hapus satu digit (*backspace*) dan tombol bersihkan semua.

### C. Terminal Kasir POS Mini Ultra Cepat ($\le 3$ Detik) (`Pages/Pos/Terminal.vue` - FR-3.4)
- **Desain Kartu Grafit Gelap (Graphite Card Focus):**
  - Mengadopsi band instrumen gelap khas Cobalt (`oklch(22% 0.016 260)`) bergaris rambut tipis, mengurangi kelelahan mata kasir di bawah terik matahari tenda bazar.
  - **Header Shift Kasir:** Nomor Stand (`JetBrains Mono`), Nama UMKM, Total Omzet Shift berjalan, dan Frekuensi Transaksi hari ini.
  - **Dropdown & Fast-Pick Menu Cards:**
    - Dropdown menu cerdas yang otomatis mengisi harga satuan seketika.
    - Baris tombol pintas (*quick chips*) untuk menu-menu terlaris stan.
  - **Stepper Kuantitas Taktil:**
    - Tombol minus (`-`), angka kuantitas, tombol plus (`+`), serta tombol pintas kilat (`+1`, `+2`, `+5`).
  - **Tombol Metode Pembayaran Kontras Tinggi:**
    - Dua tombol besar berdampingan: **TUNAI (Cash)** beraksen Emerald dan **QRIS** beraksen Electric Cobalt.
  - **Tombol Raksasa "SIMPAN TRANSAKSI":**
    - Tombol eksekusi bervolume besar dengan umpan balik multi-indera (getar ponsel `navigator.vibrate`, efek visual sukses, dan auto-reset kuantitas ke angka 1).
  - **Modal Tambah Menu Baru Kilat:**
    - Kasir dapat mendaftarkan menu baru langsung dari antarmuka POS tanpa meninggalkan sesi kasir.

### D. Lembar Desain Menu Stand Siap Cetak (`Pages/Pos/ExportMenu.vue` - FR-3.3)
- Halaman cetak daftar menu meja stan:
  - Format tabel teknis bergaris rambut 1px yang mengadopsi warna tema event.
  - Kotak barcode QRIS pedagang dengan ukuran proporsional.
  - Mode cetak otomatis (`@media print`) untuk kertas A4 Portrait.

---

## 2. Struktur Komponen yang Dibangun

```text
resources/js/
├── Pages/
│   ├── Stands/
│   │   └── Assign.vue             <-- Form Pendaftaran UMKM & Banner Edukasi
│   └── Pos/
│       ├── Login.vue              <-- Papan Tombol PIN Touchscreen 6-Digit
│       ├── Terminal.vue           <-- Terminal POS Mini Graphite Card <= 3 Detik
│       └── ExportMenu.vue         <-- Lembar Menu Meja Siap Cetak
├── Components/
│   └── Pos/
│       ├── OfficialNoticeBanner.vue <-- Banner Resmi Pemkab Edukasi Bantuan
│       ├── TactileNumpad.vue      <-- Keypad Angka Layar Sentuh Responsif
│       ├── FastMenuSelector.vue   <-- Pemilih Menu Cepat & Dropdown Harga
│       ├── QuantityStepper.vue    <-- Stepper Kuantitas Taktil (+ / -)
│       └── QuickAddMenuModal.vue  <-- Modal Kilat Tambah Menu dari Terminal
```

---

## 3. Spesifikasi Ergonomi Kecepatan $\le 3$ Detik (BRD Target)

1. Dropdown menu langsung mengaktifkan kuantitas default 1 dan menghitung subtotal secara lokal (*client-side reactive*).
2. Pilihan Tunai atau QRIS dapat diklik dalam 1 ketukan.
3. Tombol "SIMPAN TRANSAKSI" menggunakan `preserveScroll: true` via Inertia `useForm` untuk mencegah lonjakan layar (*zero layout shift*).
4. Respons sukses langsung me-reset kuantitas kembali ke 1 dan mengosongkan antrean input untuk pembeli berikutnya.

---

## 4. Alur Integrasi Endpoint Backend

- `GET /stands/{id}/assign` $\to$ Render `Pages/Stands/Assign.vue`.
- `POST /stands/{id}/assign` $\to$ Simpan profil UMKM & menu awal.
- `GET /pos/{id}/login` $\to$ Render `Pages/Pos/Login.vue`.
- `POST /pos/{id}/login-pin` $\to$ Autentikasi PIN kasir 6 digit.
- `GET /pos/{id}` $\to$ Render `Pages/Pos/Terminal.vue` (middleware `pos.pin`).
- `POST /pos/{id}/transactions` $\to$ Catat transaksi atomik (throttle 60 req/min).
- `POST /pos/{id}/menus` $\to$ Tambah menu baru langsung dari terminal.
- `GET /pos/{id}/export-menu` $\to$ Render `Pages/Pos/ExportMenu.vue`.

---

## 5. Kriteria Penerimaan (Acceptance Criteria)
- [ ] Banner edukasi resmi Diskop-UKM tampil mencolok pada form registrasi dan terminal kasir.
- [ ] Papan angka PIN layar sentuh responsif dengan auto-submit pada digit ke-6.
- [ ] Transaksi kasir POS Mini dapat diselesaikan dalam waktu kurang dari 3 detik.
- [ ] Lembar menu meja stan tercetak rapi dengan garis rambut 1px dan warna tema event.
- [ ] `npm run build` sukses 100%.
