# Frontend Technical Staging Plan: STAGE 7 (Cobalt Edition)
**Nama Tahapan:** Cross-Cutting Integration, Mobile Field Ergonomics, Hallmark Slop-Test Audit & Production Vite Build  
**Target Modul:** Uji Responsivitas Layar Ponsel Lapangan (360px–414px), Audit Anti-AI-Slop Hallmark, Aksesibilitas WCAG AA, Ketahanan Koneksi Lapangan Terbuka, dan Kompilasi Produksi Vite Bebas Warning  
**Standar Hallmark:** Lolos 100% Slop-Test Gate (Anti-Pattern Checklist), Dukungan Penuh `prefers-reduced-motion`, Zero Console Errors, dan Sukses Manifes Produksi  

---

## 1. Analisis Kebutuhan & Spesifikasi Desain Cobalt

### A. Audit 5 Anti-Pattern Hallmark (Slop-Test Gates)
Seluruh halaman frontend diuji secara ketat terhadap 5 *anti-patterns* Hallmark:

| No | Anti-Pattern AI-Slop Umum | Hallmark Cobalt Fix yang Diterapkan di PATEN | Status Audit |
| :-: | :--- | :--- | :---: |
| **01** | *The purple-gradient hero* (Gradien ungu ke biru/pink latar belakang). | Latar kanvas sejuk `oklch(98.5% 0.004 250)` + satu aksen Electric Cobalt (< 5% viewport). | **COMPLIANT** |
| **02** | *Inter as display* (Satu font tunggal dipakai untuk judul dan isi). | Pasangan berkarakter: `Space Grotesk` (Display) + `Inter` (Body) + `JetBrains Mono` (Labels/Kbd). | **COMPLIANT** |
| **03** | *Centred-everything* (Semua teks, tombol, dan kolom di-center simetris). | Ritme asimetris (Left-biased display & Right-aligned live instrument card / terminal demo). | **COMPLIANT** |
| **04** | *The icon-tile feature card* (Kartu berulang dengan kotak ikon warna-warni). | Kartu instrumen bergaris rambut 1px (`var(--color-rule)`), tipografi memimpin, tanpa ikon generic tile. | **COMPLIANT** |
| **05** | *The floating pill CTA* (Tombol kapsul mengambang dengan gradien warna). | Tombol kontrol geometris presisi radius 6px dengan warna solid tegas Electric Cobalt. | **COMPLIANT** |

### B. Ergonomi Ponsel Bazar Lapangan Terbuka (Outdoor Viewport 360px - 414px)
- **Kondisi Lapangan:** Kasir stan bazar UMKM menggunakan smartphone pribadi di bawah terik matahari tenda bazar.
- **Standar Ergonomi:**
  - Target sentuh tombol kasir, numpad PIN, dan tombol Simpan Transaksi minimal $48\text{px} \times 48\text{px}$.
  - Kontras teks dan angka nominal berada di atas rasio WCAG AA ($4.5:1$).
  - Tata letak form satu kolom yang tidak memerlukan scrolling berlebihan.

### C. Ketahanan Jaringan & Proteksi Konkurensi
- **Field Network Indicator:** Komponen status jaringan mini yang mendeteksi perubahan `window.addEventListener('online')` dan `offline`.
- **Pencegahan Double-Submit:**
  - Tombol simpan transaksi kasir dan submit tiket otomatis dinonaktifkan (*disabled state*) dengan animasi loading ramping selama request sedang berlangsung.

---

## 2. Rincian Uji Responsivitas & Viewport Matrix

| Viewport | Perangkat Sasaran | Halaman Prioritas | Parameter Uji Ergonomi |
| :--- | :--- | :--- | :--- |
| **360px - 414px** | Smartphone Kasir & Pengunjung Bazar | `/pos/{id}`, `/pos/{id}/login`, `/e/{slug}` | Tombol keypad PIN $\ge 48\text{px}$, tombol Tunai/QRIS kontras tinggi, modal e-kupon satu layar. |
| **768px - 1024px** | Tablet Panitia Gate & Supervisor Acara | `/cms/{slug}` (Gate Checkin), `/stands/{id}/assign` | Dialog scanner tiket fleksibel, tabel peserta terstruktur, kartu batch stand 2-3 kolom. |
| **1280px - 1920px** | Laptop/Desktop Tim Diskop-UKM & EO | `/cms/{slug}`, `/cms/{slug}/evaluations` | Meja kerja SPK lebar penuh, perbandingan 5 kriteria SAW berdampingan, visualisasi kuota 200 hibah APBD. |

---

## 3. Motion & Aksesibilitas (`prefers-reduced-motion`)

- **Animasi Terukur:** Seluruh efek fade + rise ~10px diatur dengan transisi halus `cubic-bezier(0.16, 1, 0.3, 1)` ~600ms.
- **Dukungan Reduced Motion:**
  ```css
  @media (prefers-reduced-motion: reduce) {
    .reveal, [data-animate] {
      opacity: 1 !important;
      transform: none !important;
      transition: none !important;
    }
  }
  ```
- **Keyboard Navigation:**
  - Shortcut `⌘K` / `Ctrl+K` dapat diakses dari halaman mana pun untuk membuka command palette.
  - Seluruh modal dapat ditutup dengan tombol `Escape`.

---

## 4. Kompilasi Produksi Vite & Audit Aset

- **Verifikasi Manifes Vite:**
  - Menjalankan `npm run build` dan memverifikasi `public/build/manifest.json` memetakan seluruh file `resources/js/Pages/**/*.vue` tanpa galat.
- **Zero Console Warnings:**
  - Memastikan tidak ada peringatan reaktivitas Vue, *unhandled exceptions*, atau *duplicate keys*.
- **Backend Test Suite Regresi:**
  - Memastikan seluruh 44 pengujian otomatis backend tetap berstatus 100% PASS saat diuji bersamaan dengan aset frontend baru.

---

## 5. Kriteria Penerimaan (Acceptance Criteria)
- [ ] Lolos seluruh 5 gate Slop-Test Hallmark tanpa pelanggaran estetika.
- [ ] Seluruh halaman responsif dan nyaman digunakan pada resolusi smartphone (360px–414px).
- [ ] Tombol transaksi POS memiliki proteksi double-submit dan feedback taktil.
- [ ] Mode `prefers-reduced-motion` berfungsi melumpuhkan animasi bagi pengguna yang membutuhkannya.
- [ ] Perintah `npm run build` berhasil menghasilkan manifes produksi tanpa kesalahan.
