# Frontend Technical Staging Plan: STAGE 4 (Cobalt Edition)
**Nama Tahapan:** Modul 2 – CMS Hidden URL Console, ⌘K Integration, Batch Stand Provisioning, Gate Terminal & Kinetic Doorprize Machine  
**Target Modul:** Dashboard Pengelola Acara via Token Rahasia (`/cms/{slug}?token=...`), 4 Kartu KPI Instrumen, CRUD Rundown, Penyediaan Slot Stand Massal & PIN Kasir, Terminal Gate, dan Pengundian Doorprize Kinetik  
**Standar Hallmark:** Bilah Kontrol Token Presisi, 4 Kartu KPI Instrumen Bergaris Rambut, Animasi Slot Kinetik (No Gimmick), dan 1-Klik Salin Tautan  

---

## 1. Analisis Kebutuhan & Spesifikasi Desain Cobalt

### A. Secret Token Bar & Dashboard Instrumen (FR-2.1)
Dashboard pengelola acara dilindungi oleh `VerifyCmsTokenMiddleware`:
- **Bilah Manajemen Token 64-Karakter:**
  - Header presisi di bagian atas dengan lencana `JetBrains Mono`: `ENCRYPTED SESSION // TOKEN: {masked_token}`.
  - Tombol aksi 6px radius: "Salin Link CMS Rahasia" (1-klik copy ke clipboard dengan toast notifikasi Cobalt).
  - Tombol pintas: "Buka Portal Publik Acara" (`/e/{slug}`) dan tombol `⌘K Perintah`.
- **4 Kartu KPI Instrumen (Instrument KPI Cards):**
  - Menggunakan font `Space Grotesk` untuk angka metrik dengan kelas tabular numbers (`tnum`):
    1. *Total Agregat Perputaran Uang (GMV):* Format rupiah rapi (misal: `Rp 48.750.000`).
    2. *Total Transaksi Kasir:* Akumulasi transaksi dari seluruh stand yang tercatat.
    3. *Total Kehadiran Gate:* Jumlah tiket ter-scan di pintu masuk lapangan.
    4. *Stand Terdaftar / Aktif:* Jumlah slot pedagang yang telah diasosiasikan.
  - Setiap kartu dikelilingi border 1px `var(--color-rule)` dengan sudut 10px radius (tanpa drop-shadow tebal).

### B. Rundown Management Studio (FR-2.2)
- Tabel manajemen jadwal sesi kegiatan bergaris rambut 1px:
  - Kolom Urutan, Jam Sesi (`JetBrains Mono`), Judul Sesi (`Space Grotesk`), Pengisi Acara, dan Tombol Aksi.
  - Modal Tambah Sesi & Edit Sesi dengan input waktu presisi.
  - Tombol hapus dengan konfirmasi dialog aman.

### C. Stand & Facility Provisioning Manager (FR-2.3)
- Pengaturan kuota stan pedagang UMKM:
  - **Batch Generator Modal:** Panitia memasukkan jumlah stan yang ingin dibuat otomatis (misal: 10 stand, 25 stand), memilih kategori default, daya listrik (Watt), dan checklist fasilitas (Meja, Kursi, Tenda Sarnafil, Air Bersih).
  - **Grid Kartu Stand Geometris:**
    - Nomor stand dalam `JetBrains Mono` tebal (misal: `STAND-01`).
    - Lencana Status: `KOSONG` / `TERISI UMKM`.
    - **PIN Kasir 6-Digit:** Ditampilkan dengan tombol salin cepat 1-klik untuk dibagikan ke kasir.
    - **Tautan Registrasi UMKM:** Tombol salin link pendaftaran (`/stands/{id}/assign`) untuk dikirim via WhatsApp.

### D. Gate Check-in Terminal & Kinetic Doorprize Machine (FR-2.4)
- **Terminal Scanner Tiket di Pintu Masuk:**
  - Kotak input cepat kode tiket / kupon dengan autofocus.
  - Log kehadiran *realtime stream* dengan indikator status: Hijau (Valid) atau Merah (Kupon Salah / Sudah Pernah Masuk).
- **Kinetic Doorprize Machine (Pengundian Acak):**
  - Tombol pemicu: "Putar Undian Doorprize" (Solid Cobalt 6px).
  - **Animasi Pengacak Kinetik:** Mengacak daftar nama pengunjung hadir dengan efek interval teks yang melompat cepat sebelum berhenti pada nama pemenang resmi dari API `POST /cms/{slug}/draw-doorprize`.
  - **Modal Pemenang Doorprize:** Menampilkan nama lengkap, nomor tiket kupon, kecamatan, dan tombol cetak berita acara pemenang.

---

## 2. Struktur Komponen yang Dibangun

```text
resources/js/
├── Pages/
│   └── Cms/
│       └── Dashboard.vue          <-- Dashboard Utama CMS Hidden URL
├── Components/
│   └── Cms/
│       ├── SecretTokenBar.vue     <-- Bilah Informasi Token & Tautan Clipboard
│       ├── InstrumentKpiGrid.vue  <-- Grid 4 Kartu KPI Instrumen Bergaris Rambut
│       ├── RundownTableEditor.vue <-- Tabel Editor CRUD Jadwal Acara
│       ├── StandBatchModal.vue    <-- Modal Generator Batch Slot Stand
│       ├── StandCardGrid.vue      <-- Grid Kartu Stand Presisi dengan PIN Kasir
│       ├── GateScannerPanel.vue   <-- Terminal Validasi Tiket di Pintu Masuk
│       └── KineticDoorprize.vue   <-- Pengundian Doorprize Acak Beranimasi Kinetik
```

---

## 3. Spesifikasi Rinci Antarmuka & UX

### A. `SecretTokenBar.vue`
- Menampilkan peringatan keamanan tenang: *"Simpan tautan ini dengan aman. URL ini memberikan hak akses penuh manajemen acara tanpa login akun."*
- Mengintegrasikan tombol pintas keyboard `⌘K` untuk membuka palet perintah cepat.

### B. `KineticDoorprize.vue`
- Menampilkan kanvas instrumen gelap grafit (`oklch(22% 0.016 260)`) bergaris rambut 1px.
- Teks nama peserta berputar secara kinetik selama 3 detik sebelum mengunci pemenang akhir yang valid dari database.

---

## 4. Alur Integrasi Endpoint Backend

- Seluruh request menyertakan `?token={props.token}` sesuai middleware `cms.token`.
- `GET /cms/{slug}` $\to$ Render `Pages/Cms/Dashboard.vue` dengan data metrik, rundown, stand, dan pengunjung.
- `POST /cms/{slug}/rundowns` $\to$ Tambah sesi rundown baru.
- `DELETE /cms/{slug}/rundowns/{id}` $\to$ Hapus sesi rundown.
- `POST /cms/{slug}/stands` $\to$ Eksekusi pembuatan batch slot stand & PIN kasir.
- `POST /cms/{slug}/check-in` $\to$ Validasi kode tiket pengunjung di gate.
- `POST /cms/{slug}/draw-doorprize` $\to$ Undian acak pemenang doorprize.

---

## 5. Kriteria Penerimaan (Acceptance Criteria)
- [ ] Bilah token rahasia memfasilitasi salin tautan 1-klik dengan umpan balik toast instan.
- [ ] 4 KPI metrik finansial & keramaian tampil terstruktur menggunakan font Space Grotesk dan angka tabular.
- [ ] Generator slot stand menghasilkan kartu stand lengkap dengan PIN 6 digit dan link assign UMKM.
- [ ] Terminal check-in gate memverifikasi tiket secara cepat dan responsif.
- [ ] Pengundian doorprize menyajikan animasi kinetik yang elegan dan bebas gimmick norak.
- [ ] `npm run build` sukses 100%.
