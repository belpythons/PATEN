# Frontend Technical Staging Plan: STAGE 6 (Triad Color Edition)
**Nama Tahapan:** Modul 4 – SPK & ML Instrument Panel Dashboard, Perbup Guardrail Inspector & 5-Program Aid Allocation Matrix  
**Target Modul:** Dashboard Analisis Keputusan SPK & ML (`/cms/{slug}/evaluations`), Inspektur Guardrail Regulasi Perbup Kutim, Matriks Pembobotan SAW 5 Kriteria, Pemetaan 5 Program Bantuan (Highlight Kuota 200 APBD), dan Lembar Laporan Eksekutif  
**Standar Hallmark:** Tampilan Meja Kerja Instrumen (Workbench Register), Lencana Guardrail Teknis (Mono Status), Matriks Data Bergaris Rambut 1px, Palet Triad (#132952, #00B262, #FAA21B), dan Cetak Rekap Resmi  

---

## 1. Analisis Kebutuhan & Spesifikasi Desain Triad

### A. Dashboard Analisis SPK Berkarakter Meja Kerja (*Workbench*) (FR-4.1 & FR-4.2)
Halaman `/cms/{slug}/evaluations` diakses oleh pengelola acara dan tim penilai Dinas Koperasi & UKM Kutai Timur:
- **Header Meja Kerja Teknis:**
  - *Eyebrow Mono:* `04 / SISTEM PENDUKUNG KEPUTUSAN & PIPELINE ML KUTIM`.
  - *Title:* *"Matriks Evaluasi Performa Niaga & Rekomendasi Program Bantuan"* dalam `Space Grotesk`.
  - *Metrik Ringkas:* Total Stan Dinilai, Total Lolos Syarat Perbup, dan Alokasi Kuota Prioritas 200 Hibah APBD yang terisi.
  - **Tombol Eksekusi Terpadu:** "Jalankan Evaluasi SPK & Guardrail Perbup" (Solid Deep Navy #132952 6px) dengan indikator kalkulasi instan.

### B. Inspektur Guardrail Regulasi Perbup Kutim (FR-4.1)
Verifikasi kepatuhan terhadap 4 aturan mutlak Perbup Kutai Timur disajikan melalui lencana status monospace:
- **Lencana Lolos:**
  - `[✓ LOLOS PERBUP]` dengan aksen hijau sinyal *Vibrant Emerald Green* (`#00B262`) dan garis rambut 1px.
- **Lencana Diskualifikasi dengan Alasan Spesifik (Zero Ambiguity):**
  - `[✗ DISKUALIFIKASI: DUPLIKASI NO KK]` — Aturan 1 KK = 1 Usaha; UMKM lain dalam KK yang sama dengan skor lebih rendah dialihkan ke program non-hibah APBD.
  - `[✗ DISKUALIFIKASI: NIB < 6 BULAN]` — Masa aktif NIB belum memenuhi syarat minimal 6 bulan.
  - `[✗ DISKUALIFIKASI: PERNAH TERIMA HIBAH]` — Anti-duplikasi bantuan modal berjalan.

### C. Matriks Skor SAW (Simple Additive Weighting) (PRD Bab 3)
Tabel instrumen data lebar penuh (*full-width data table*) dengan batas sel 1px:
- Kolom Rincian 5 Kriteria Berbobot:
  - $w_1 = 30\%$ (Frekuensi Transaksi)
  - $w_2 = 20\%$ (Total Omzet)
  - $w_3 = 25\%$ (Konsistensi Jam Aktif)
  - $w_4 = 10\%$ (Rasio QRIS)
  - $w_5 = 15\%$ (Kelengkapan Legalitas)
- **Skor Akhir SPK ($0 - 100$):** Angka tabular tebal didukung bar visual mini bergaris rambut.

### D. Pemetaan 5 Jalur Program Bantuan Kutai Timur (BRD Bab 4 & PRD FR-4.2)
Setiap UMKM dikelompokkan ke dalam satu rekomendasi intervensi resmi dengan palet warna Triad terpadu:
1. `Fasilitasi Legalitas Usaha (NIB/Halal/PIRT/HKI)` — Border Deep Navy Blue (`#132952`).
2. `Bantuan Modal Awal BAZNAS & TKM (Rp 5 Juta)` — Border Slate Netral (`#CBD5E1`).
3. `Hibah Peralatan Produksi Pemkab / CSR` — Border Warm Amber Orange (`#FAA21B`).
4. `Hibah Permodalan APBD Kutim (Rp 5 - 25 Juta)` — **Highlight Warm Amber Orange (#FAA21B) & Lencana Prioritas Kuota 200 APBD**.
5. `Kredit Usaha BPR Kutim / KUR Bankaltimtara & Inkubasi KPC` — Border Vibrant Emerald Green (`#00B262`).

### E. Toolbar Penyaring Interaktif & Laporan Eksekutif
- **Filter Cepat:**
  - Dropdown 18 Kecamatan Kutim (Sangatta Utara, Sangatta Selatan, Bengalon, Kongbeng, Muara Wahau, dll.).
  - Dropdown Kategori Usaha (Kuliner, Kriya, Fesyen, Agribisnis, Jasa).
  - Dropdown Status Kelayakan (Semua, Lolos Perbup Saja, Kuota Prioritas Hibah Saja).
  - Kolom pencarian teks instan (Nama Usaha, Pemilik, NIK, Nomor Stand).
- **Tombol Ekspor Laporan Rekapitulasi:**
  - Menampilkan lembar cetak resmi berita acara hasil seleksi untuk ditandatangani Kepala Dinas dan diajukan kepada Bupati Kutai Timur.

---

## 2. Struktur Komponen yang Dibangun

```text
resources/js/
├── Pages/
│   └── Cms/
│       └── Evaluations.vue         <-- Meja Kerja Evaluasi SPK & Rekomendasi Bantuan
├── Components/
│   └── Spk/
│       ├── SpkHeaderMetrics.vue    <-- Metrik Ringkasan Status Evaluasi & Kuota
│       ├── GuardrailInspectorBadge.vue <-- Lencana Status Kepatuhan Regulasi Perbup
│       ├── SawMatrixTable.vue      <-- Tabel Matriks 5 Kriteria SAW Bergaris Rambut
│       ├── ProgramAllocationBadge.vue <-- Lencana 5 Jalur Program Bantuan Kutim
│       ├── SpkFilterToolbar.vue    <-- Toolbar Penyaring 18 Kecamatan & Kategori
│       └── ExecutiveReportModal.vue <-- Modal Lembar Rekap Hasil Evaluasi Siap Cetak
```

---

## 3. Spesifikasi Rinci Antarmuka & UX

### A. SawMatrixTable.vue
- Menggunakan `overflow-x: auto` dengan kolom nomor stand yang terkunci (*sticky first column*).
- Baris tabel untuk nominasi *Top Prioritas Hibah APBD (Kuota 200)* memiliki latar highlight aksen lembut dan lencana mahkota emas.

### B. ExecutiveReportModal.vue
- Memformat rekapitulasi ke dalam template surat dinas resmi Pemkab Kutai Timur dengan kop surat, tabel nominasi terurut peringkat, dan area tanda tangan berita acara.

---

## 4. Alur Integrasi Endpoint Backend

- `GET /cms/{slug}/evaluations?token={token}` $\to$ Render `Pages/Cms/Evaluations.vue` dengan data evaluasi lengkap.
- `POST /cms/{slug}/evaluate-spk?token={token}` $\to$ Memicu kalkulasi SPK SAW, evaluasi guardrail Perbup, sinkronisasi webhook ML, dan pembaruan database.
- Hasil penilaian langsung diperbarui secara reaktif via Inertia reload state tanpa refresh browser.

---

## 5. Kriteria Penerimaan (Acceptance Criteria)
- [ ] Lencana verifikasi Guardrail Perbup menyajikan status lolos/diskualifikasi dengan alasan regulasi yang eksplisit.
- [ ] Matriks 5 kriteria berbobot SAW terinci rapi dalam tabel instrumen data bergaris rambut 1px.
- [ ] Pemetaan 5 Program Bantuan Kutai Timur menonjolkan Kuota Prioritas 200 Hibah APBD.
- [ ] Filter kecamatan dan pencarian instan berfungsi responsif.
- [ ] `npm run build` sukses 100%.
