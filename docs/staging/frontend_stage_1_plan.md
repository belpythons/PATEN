# Frontend Technical Staging Plan: STAGE 1 (Triad Color Edition)
**Nama Tahapan:** Core Design System, Triad Palette Tokens, Master Layouts & Working ⌘K Command Palette  
**Target Modul:** Infrastruktur Desain, Font Stack (Space Grotesk + Inter + JetBrains Mono), Palet Warna Triad (Deep Navy Blue #132952, Vibrant Emerald Green #00B262, Warm Amber Orange #FAA21B), Layout Publik & CMS dengan ⌘K, serta Pustaka Komponen UI Geometris 6px/10px  
**Standar Hallmark:** Lolos Slop-Test (Tanpa gradien sembarangan, tanpa border radius pil, border 1px ruler-drawn), Kontras WCAG AA, dan Sukses `npm run build`  

---

## 1. Spesifikasi Teknis Token Triad

### A. Palet Token Desain (`resources/css/app.css`)
```css
@import 'tailwindcss';

@theme {
  --color-paper: #F8FAFC;                     /* Cool near-white canvas */
  --color-paper-muted: #F1F5F9;               /* Subdued section background */
  --color-ink-primary: #1E293B;               /* Cool charcoal for headings */
  --color-ink-secondary: #334155;             /* Body text */
  --color-ink-muted: #64748B;                 /* Micro-labels & timestamps */
  --color-navy: #132952;                      /* Deep Navy Blue primary anchor */
  --color-navy-hover: #0E1E3D;                /* Darker deep navy */
  --color-navy-subtle: #EDF2F9;               /* Subtle navy tint */
  --color-emerald: #00B262;                   /* Vibrant Emerald Green success signal */
  --color-emerald-hover: #009954;             /* Darker emerald */
  --color-emerald-subtle: #E6F7EF;            /* Subtle emerald tint */
  --color-amber: #FAA21B;                     /* Warm Amber Orange accent/gamifikasi */
  --color-amber-hover: #E08E10;               /* Darker amber */
  --color-amber-subtle: #FEF6E8;              /* Subtle amber tint */
  --color-rule: #E2E8F0;                      /* 1px hairline border */
  --color-rule-dark: #CBD5E1;                 /* Emphasized divider */
  --color-graphite: #0F172A;                  /* Dark card for terminal & code */

  --font-display: 'Space Grotesk', -apple-system, sans-serif;
  --font-body: 'Inter', -apple-system, sans-serif;
  --font-mono: 'JetBrains Mono', monospace;

  --radius-control: 6px;   /* Buttons, inputs, chips (never pills) */
  --radius-surface: 10px;  /* Cards, terminals, modals (drawn with a ruler) */
}
```

### B. Font Integration (`resources/views/app.blade.php`)
Menyematkan Google Fonts resmi di `<head>`:
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
```

---

## 2. Struktur Komponen yang Dibangun

```text
resources/js/
├── Layouts/
│   ├── PublicLayout.vue       <-- Layout Publik (Bordered Nav, Brand Mark, Footer Colophon)
│   ├── CmsLayout.vue          <-- Layout CMS Admin (Secret Token Bar, ⌘K Button, Event Status)
│   └── PosLayout.vue          <-- Layout Kasir POS (Ergonomis Layar Penuh, Indikator Sesi)
├── Components/
│   ├── UI/
│   │   ├── BaseButton.vue     <-- Tombol 6px Radius (Primary Navy, Emerald, Amber, Outline)
│   │   ├── BaseInput.vue      <-- Input 6px Radius dengan Font Inter & Border Hairline 1px
│   │   ├── BaseSelect.vue     <-- Dropdown Presisi dengan Label Monospace
│   │   ├── BaseTextarea.vue   <-- Input Area Teks 6px Radius
│   │   ├── BaseCard.vue       <-- Kontainer 10px Radius Bergaris Rambut (Hairline 1px)
│   │   ├── BaseModal.vue      <-- Dialog 10px Radius (Escape & Backdrop Dismiss)
│   │   ├── BaseBadge.vue      <-- Lencana Teknis (Mono Uppercase, 0.06em Tracking)
│   │   ├── BaseToast.vue      <-- Toast Notifikasi Reaktif (Navy & Emerald Signals)
│   │   └── StatCard.vue       <-- Kartu Instrumen Metrik dengan Nilai Angka Tabular
│   └── Navigation/
│       ├── GlobalCommandPalette.vue <-- Modal ⌘K Command Palette Interaktif (Ciri Khas Triad)
│       └── BrandMark.vue      <-- Logo Teks Ramping "/paten" Bergaya Hallmark
```

---

## 3. Spesifikasi Komponen Kunci Hallmark Triad

### A. `GlobalCommandPalette.vue` (Signature Move)
- **Affordance Navigasi:** Menampilkan tombol ramping dengan teks label `Cari atau perintah...` dan tombol pintas `<kbd>⌘K</kbd>`.
- **Keyboard Listener:** Mendeteksi kombinasi tombol `Ctrl+K` atau `Cmd+K` pada sistem operasi pengguna, serta `Escape` untuk menutup.
- **Daftar Perintah Cepat:**
  - *Navigasi Halaman:* Beranda Publik (`/`), Buat Event Baru (`/events/create`), Dashboard CMS (`/cms/{slug}`).
  - *Aksi Langsung:* Jalankan Evaluasi SPK, Buka Klasemen Awarding, Cetak Poster Acara, Salin Link Token CMS.
  - *Penyaring Instan:* Filter cepat berdasarkan input teks dengan penyorotan keyboard (Panah Atas / Bawah + Enter).

### B. `BaseButton.vue` (Tight 6px Control)
- **Aturan Hallmark:** Dilarang menggunakan tombol bentuk pil bulat (*pill-shaped buttons*). Tombol memiliki radius `6px` presisi.
- **Varian:**
  - `primary`: Latar belakang solid *Deep Navy Blue* (`#132952`), teks putih bersih, efek hover `#0E1E3D`.
  - `emerald`: Latar belakang solid *Vibrant Emerald Green* (`#00B262`), teks putih bersih, efek hover `#009954`.
  - `amber`: Latar belakang solid *Warm Amber Orange* (`#FAA21B`), teks `#132952` kontras tinggi / gelap.
  - `secondary` / `outline`: Latar transparan, border 1px `var(--color-rule)`, teks `var(--color-ink-primary)`, hover border bergeser ke Navy.
  - `danger`: Teks merah dengan border 1px lembut.
- **States:** Menampilkan spinner ramping ketika `loading: true` dan menonaktifkan klik.

### C. `BaseBadge.vue` (Monospace Technical Label)
- Menggunakan `font-mono` (`JetBrains Mono`), ukuran teks `0.75rem` (`text-xs`), teks UPPERCASE, dan `letter-spacing: 0.06em`.
- Varian warna terstandarisasi: `navy` (`#132952`), `emerald` (`#00B262`), `amber` (`#FAA21B`), dan `graphite`.
- Memberikan impresi *machine-readout* instrumen teknologi terpercaya.

---

## 4. Alur Integrasi & Uji Coba

1. **Konfigurasi CSS:**
   - Memastikan `resources/css/app.css` mendefinisikan seluruh variabel Triad Palette (`#132952`, `#00B262`, `#FAA21B`) dan utility typography.
2. **Setup Single Root Element:**
   - Seluruh layout dan komponen Vue membungkus markup dalam satu elemen root tunggal.
3. **Verifikasi Build:**
   - Menjalankan `npm run build` untuk memverifikasi seluruh komponen terkompilasi tanpa error.

---

## 5. Kriteria Penerimaan (Acceptance Criteria)
- [ ] Font Space Grotesk, Inter, dan JetBrains Mono termuat dengan baik.
- [ ] Token palet Triad (`#132952`, `#00B262`, `#FAA21B`) aktif dan diaplikasikan pada seluruh komponen dasar.
- [ ] Tersedia `GlobalCommandPalette.vue` yang aktif via klik dan shortcut keyboard `Ctrl+K` / `Cmd+K`.
- [ ] Seluruh tombol menggunakan radius teknis 6px tanpa bentuk pil bulat.
- [ ] `npm run build` sukses 100%.
