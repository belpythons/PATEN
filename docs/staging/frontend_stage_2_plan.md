# Frontend Technical Staging Plan: STAGE 2 (Triad Color Edition)
**Nama Tahapan:** Modul 1 – Asymmetric Event Generator, Triad Theme Preview, Lock Confirmation Modal & Ruler-Drawn Poster Export  
**Target Modul:** Pembuatan Event (`/events/create`), Pemilih Warna Triad Presisi (FR-1.3), Dialog Penguncian (FR-1.2), dan Generator Poster Cetak Ber-QR (`/events/{slug}/poster-export`)  
**Standar Hallmark:** Tata Letak Asimetris (Title-Left, Card-Right), Palet Triad (#132952, #00B262, #FAA21B), Kontur Garis Rambut 1px, dan Tipografi Space Grotesk / JetBrains Mono  

---

## 1. Analisis Kebutuhan & Spesifikasi Desain Triad

### A. Asymmetric Event Wizard (`Pages/Events/Create.vue` - FR-1.1 & FR-1.2)
Mengikuti kaidah Hallmark: **Hindari tampilan semua serba di tengah (*Centred-everything*)**. Halaman pembuatan event mengadopsi ritme asimetris dua kolom:
- **Kolom Kiri (Input & Form Wizard):**
  - *Eyebrow Label:* `JetBrains Mono` uppercase: `01 / INISIALISASI INFRASTRUKTUR EVENT`.
  - *Display Heading:* `Space Grotesk` (600, tracking rapat): *"Buat Ekosistem Event & Bazar UMKM"*.
  - *Lede:* Keterangan ringkas dalam `Inter` 400.
  - *Form Sections:*
    1. **Identitas Acara:** Nama Event, Penyelenggara resmi, Deskripsi ringkas, Input Tags Sponsor.
    2. **Jadwal & Lokasi:** Venue di Kutai Timur, Kalender & Waktu Mulai–Selesai dengan batas validasi tanggal.
    3. **Skema Warna & Tema (FR-1.3):**
       - Mode Palet: Radio box 6px untuk `Mono` (1 Warna Hex) vs `Duo` (Primer & Sekunder).
       - Palet Triad Wajib: Deep Navy Blue (`#132952`), Vibrant Emerald Green (`#00B262`), Warm Amber Orange (`#FAA21B`).
       - Color input picker manual dengan validasi format Regex Hex realtime.
    4. **Gamifikasi Publik (FR-1.4):**
       - Toggle seleksi: `doorprize` (Kupon Gratis Warga), `htm` (Tiket Masuk), `both`, atau `none`.
       - Sakelar *Awarding UMKM Teraktif* (Top 1 / Top 3 / Top 5).
    5. **Initial Rundown Builder:** Baris jadwal kegiatan pembukaan yang dinamis.
- **Kolom Kanan (Live Instrument Preview Card):**
  - **The Live Triad Card:** Kartu pratinjau mini 10px radius dengan border hairline 1px yang langsung merefleksikan perubahan warna primer & sekunder, nama event, serta simulasi tampilan badge tiket secara instan tanpa reload.

### B. Lock Confirmation Modal (FR-1.2)
- Sebelum form dikirim ke `POST /events`, dialog modal 10px radius muncul:
  - Ringkasan data inti: Nama Event, Estimasi Slug URL publik (`/e/{slug}`), Instansi Penyelenggara, dan Gamifikasi.
  - **Pemberitahuan Kriptografis:** *"Sistem akan mengunci konfigurasi dasar dan menerbitkan Token Manajemen CMS 64-karakter unik."*
  - Tombol aksi: "Periksa Kembali" (Outline 6px) dan "Kunci & Terbitkan Event" (Solid Navy #132952 6px).

### C. Ruler-Drawn Poster Export (`Pages/Events/PosterExport.vue` - FR-1.5)
Halaman `/events/{slug}/poster-export` merender lembar poster publikasi resmi dengan presisi geometris:
- **Toggle Format:** Pilihan format tampilan A4 Portrait (Flyer Cetak Lapangan) vs Rasio 1:1 (Feed Instagram).
- **Struktur Desain:**
  - Border luar hairline rapi dengan header instansi resmi Pemkab Kutai Timur.
  - Tipografi judul dalam font `Space Grotesk` berukuran besar.
  - Tanggal, jam, dan lokasi venue dengan penanda `JetBrains Mono`.
  - **Dynamic SVG QR Code:** Kotak instrumen QR code di sisi tengah/kanan yang mengarah ke `/e/{slug}` untuk pemindaian instan oleh smartphone warga.
- **Print Optimization (`@media print`):**
  - Menyembunyikan tombol aksi browser.
  - Menjamin ketajaman 100% pada kertas cetak fisik dan ekspor PDF.

---

## 2. Struktur Komponen yang Dibangun

```text
resources/js/
├── Pages/
│   └── Events/
│       ├── Create.vue             <-- Form Wizard Asimetris Dua Kolom
│       └── PosterExport.vue       <-- Kanvas Cetak Poster Bergaris Rambut 1px
├── Components/
│   └── Events/
│       ├── AsymmetricHero.vue     <-- Header Halaman Form dengan Eyebrow Mono
│       ├── LiveThemeCard.vue      <-- Kartu Pratinjau Interaktif Dinamis
│       ├── LockConfirmModal.vue   <-- Modal Konfirmasi Penerbitan Token
│       └── RundownRowBuilder.vue  <-- Input Baris Rundown Dinamis
```

---

## 3. Spesifikasi Rinci Antarmuka & UX

### A. Asymmetric Form Rhythm
- Rasio lebar: 60% Form Kontrol di kiri, 40% Sticky Preview Card di kanan (pada layar desktop).
- Pada layar ponsel, kolom tersusun secara vertikal dengan tombol pintas preview mengambang (*toggle preview floating button*).

### B. Live Theme Preview Card
- Menampilkan chip status: `MODE: DUO`, `HEX: #132952 / #00B262`.
- Menampilkan mockup miniatur e-kupon dengan warna yang disuntikkan secara dinamis melalui CSS Variables (`--theme-primary`, `--theme-secondary`).

---

## 4. Alur Integrasi Endpoint Backend

- `GET /events/create` $\to$ Render `Pages/Events/Create.vue`.
- `POST /events` $\to$ Simpan event secara atomik, generate slug & token rahasia 64-karakter.
- `GET /events/{slug}/poster-export` $\to$ Render `Pages/Events/PosterExport.vue` dengan payload metadata event dan SVG QR Code.

---

## 5. Kriteria Penerimaan (Acceptance Criteria)
- [ ] Tata letak form `Create.vue` menerapkan prinsip asimetris kiri-kanan secara proporsional.
- [ ] Kartu pratinjau live merefleksikan perubahan warna primer/sekunder secara real-time.
- [ ] Modal konfirmasi penguncian memvalidasi slug dan nama instansi sebelum submit.
- [ ] Poster generator pada `/events/{slug}/poster-export` menghasilkan tata letak cetak A4 tajam dengan QR code yang dapat dipindai.
- [ ] `npm run build` sukses 100%.
