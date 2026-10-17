# Frontend Technical Staging Plan: STAGE 3 (Cobalt Edition)
**Nama Tahapan:** Modul 1 (Publik) – Asymmetric Event Microsite, Precision Ticket Stub & Cobalt Awarding Leaderboard  
**Target Modul:** Microsite Publik Acara (`/e/{slug}`), Registrasi Kehadiran Instan, Modal Tiket Presisi Ber-QR, dan Klasemen Awarding Stand (`/e/{slug}/leaderboard`)  
**Standar Hallmark:** Hero Asimetris Berkarakter Instrumen, Tiket Digital Geometris 10px, Tabulasi Presisi Bergaris Rambut 1px, dan Proteksi Mutlak Sensor Omzet Rupiah  

---

## 1. Analisis Kebutuhan & Spesifikasi Desain Cobalt

### A. Microsite Publik Acara (`Pages/Events/Show.vue` - FR-1.4)
Pengunjung bazar mengakses halaman publik `/e/{slug}` lewat scan QR Code poster di lapangan:
- **Hero Instrument Layout (Asymmetric):**
  - **Kiri:** Eyebrow mono (`01 / PORTAL RESMI ACARA`), Judul Acara dalam `Space Grotesk` tebal, Lede instansi penyelenggara & venue, dan tombol pintas navigasi.
  - **Kanan (Instrument Status Card):** Kartu 10px radius bergaris rambut 1px yang memuat hitung mundur (*countdown timer*) waktu acara, lencana mode kehadiran aktif, dan tautan langsung ke klasemen stan teraktif.
- **Timeline Rundown Acara:**
  - Tampilan vertikal garis rambut 1px (`border-l border-rule`).
  - Label jam mulai dan jam selesai dalam `JetBrains Mono` (`10:00 - 12:30 WITA`).
  - Judul sesi, pengisi acara, dan lencana sesi aktif.
- **Section Registrasi Kehadiran (Gamifikasi Publik):**
  - Formulir pendaftaran satu kolom yang ergonomis: Nama Lengkap, Nomor WhatsApp, Kecamatan Domisili (pilihan dari 18 Kecamatan Kutim).
  - Pilihan Tipe Registrasi: Doorprize Kupon Gratis atau Pembelian Tiket HTM.
  - **Precision Digital Ticket Modal:**
    - Setelah pendaftaran sukses, muncul modal pop-up berkarakter *ticket stub* geometris:
    - Kode Tiket Kupon (misal: `PATEN-DOOR-8X92M`) dalam font mono besar.
    - SVG QR Code unik beresolusi tajam untuk dipindai oleh panitia di pintu masuk (*gate*).
    - Tombol aksi: "Simpan Gambar / Screenshot" dan "Salin Kode Tiket".

### B. Klasemen Publik Stand Teraktif (`Pages/Events/Leaderboard.vue` - FR-1.4 Mode C)
Halaman `/e/{slug}/leaderboard` menyajikan data kompetisi keaktifan UMKM bazar:
- **Header Teknis:**
  - Judul: *"Klasemen Keaktifan Stand UMKM"* dalam `Space Grotesk`.
  - Subteks: *"Penghargaan keaktifan dihitung berdasarkan konsistensi pelayanan dan pencatatan transaksi kasir digital selama event berlangsung."*
- **Podium & Tabulasi Peringkat Presisi:**
  - Baris Peringkat 1, 2, dan 3 disorot dengan lencana kontras bergaris rambut (Emas, Perak, Perunggu).
  - Tabel terstruktur: Kolom Peringkat, Kolom Nomor Stand (`JetBrains Mono`), Kolom Nama Usaha UMKM, Kolom Poin Keaktifan (`tnum` tabular numbers).
  - **ATURAN PRIVASI MUTLAK (Masking Finansial):**
    - Nominal omzet rupiah sama sekali **TIDAK DITAMPILKAN** ke publik.
    - Yang terlihat hanya skor poin performa niaga.

---

## 2. Struktur Komponen yang Dibangun

```text
resources/js/
├── Pages/
│   └── Events/
│       ├── Show.vue               <-- Halaman Microsite Publik Acara Asimetris
│       └── Leaderboard.vue        <-- Halaman Klasemen Stand Bergaris Rambut 1px
├── Components/
│   └── Public/
│       ├── AsymmetricHeroShow.vue <-- Hero Section Dua Kolom Khas Cobalt
│       ├── PrecisionRundown.vue   <-- Timeline Rundown Presisi Bergaris Rambut
│       ├── AttendanceBox.vue      <-- Box Formulir Kehadiran Doorprize / HTM
│       ├── TicketStubModal.vue    <-- Modal E-Kupon Tiket Geometris Ber-QR
│       └── CobaltLeaderboardTable.vue <-- Tabel Klasemen dengan Sensor Omzet
```

---

## 3. Spesifikasi Rinci Antarmuka & UX

### A. `TicketStubModal.vue`
- Kartu modal 10px radius dengan aksen garis putus-putus (*dashed divider*) yang memisahkan bagian identitas pengunjung dan area kode QR.
- Umpan balik visual saat tombol "Salin Kode" ditekan: teks berubah instan menjadi *"Tersalin!"* dengan warna Electric Cobalt.

### B. `CobaltLeaderboardTable.vue`
- Tabel dengan garis batas sel 1px `var(--color-rule)`.
- Menggunakan angka tabular (*tabular nums*) untuk poin keaktifan agar angka rata secara vertikal dan nyaman dipindai.
- Indikator penyegaran data otomatis (*auto-refresh indicator*) dengan titik sinyal berkedip halus.

---

## 4. Alur Integrasi Endpoint Backend

- `GET /e/{slug}` $\to$ Render `Pages/Events/Show.vue` dengan props `event`, `rundowns`, `qrCodeUrl`.
- `POST /e/{slug}/register-attendance` $\to$ Registrasi pengunjung (rate limited). Mengembalikan respons tiket `{ nomor_tiket, kode_kupon, qr_code_svg, nama_lengkap }`.
- `GET /e/{slug}/leaderboard` $\to$ Render `Pages/Events/Leaderboard.vue` dengan props `event`, `leaderboard`, `awarding_tier`.

---

## 5. Kriteria Penerimaan (Acceptance Criteria)
- [ ] Halaman `/e/{slug}` menerapkan tata letak asimetris kiri-kanan dengan palet warna Cobalt.
- [ ] Pendaftaran pengunjung memunculkan modal tiket stub ber-QR code tajam.
- [ ] Klasemen leaderboard publik menampilkan nomor stand, nama usaha, dan poin keaktifan tanpa kebocoran nominal rupiah omzet.
- [ ] Tampilan responsif sempurna pada layar ponsel smartphone (375px - 414px).
- [ ] `npm run build` sukses 100%.
