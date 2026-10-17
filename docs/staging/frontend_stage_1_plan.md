# Frontend Technical Staging Plan: STAGE 1 (Cobalt Edition)
**Nama Tahapan:** Core Design System, Hallmark Cobalt Tokens, Master Layouts & Working ⌘K Command Palette  
**Target Modul:** Infrastruktur Desain, Font Stack (Space Grotesk + Inter + JetBrains Mono), Palet Warna OKLCH Cobalt, Layout Publik & CMS dengan ⌘K, serta Pustaka Komponen UI Geometris 6px/10px  
**Standar Hallmark:** Lolos Slop-Test (Tanpa gradien sembarangan, tanpa border radius pil, border 1px ruler-drawn), Kontras WCAG AA, dan Sukses `npm run build`  

---

## 1. Spesifikasi Teknis Token Cobalt (OKLCH)

### A. Palet Token Desain (`resources/css/app.css`)
```css
@import 'tailwindcss';

@theme {
  --color-paper: oklch(98.5% 0.004 250);     /* Cool near-white canvas */
  --color-paper-muted: oklch(96% 0.008 250);  /* Subdued section background */
  --color-ink-primary: oklch(24% 0.02 258);   /* Cool charcoal for headings */
  --color-ink-secondary: oklch(34% 0.018 257);/* Body text */
  --color-ink-muted: oklch(55% 0.015 255);    /* Micro-labels & timestamps */
  --color-cobalt: oklch(58% 0.20 256);        /* Electric Cobalt signal (< 5%) */
  --color-cobalt-hover: oklch(52% 0.22 256);  /* Darker electric cobalt */
  --color-cobalt-subtle: oklch(95% 0.04 256); /* Tint for active chips & focus */
  --color-rule: oklch(90% 0.01 250);          /* 1px hairline border */
  --color-rule-dark: oklch(80% 0.015 250);    /* Emphasized divider */
  --color-graphite: oklch(22% 0.016 260);     /* Dark card for terminal & code */
  --color-graphite-rule: oklch(32% 0.02 260); /* Hairline on dark surfaces */
  --color-emerald-signal: oklch(62% 0.17 145);/* Verified / Success status */
  --color-ruby-signal: oklch(60% 0.21 28);    /* Disqualified / Error status */

  --font-display: 'Space Grotesk', -apple-system, sans-serif;
  --font-body: 'Inter', -apple-system, sans-serif;
  --font-mono: 'JetBrains Mono', monospace;

  --radius-control: 6px;   /* Buttons, inputs, chips (never pills) */
  --radius-surface: 10px;  /* Cards, terminals, modals (drawn with a ruler) */
}
```

### B. Font Integration (`resources/views/app.blade.php`)
Menyematkan Google Fonts resmi Cobalt di `<head>`:
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
│   │   ├── BaseButton.vue     <-- Tombol 6px Radius (Primary Cobalt, Outline Hairline, Danger)
│   │   ├── BaseInput.vue      <-- Input 6px Radius dengan Font Inter & Border Hairline 1px
│   │   ├── BaseSelect.vue     <-- Dropdown Presisi dengan Label Monospace
│   │   ├── BaseTextarea.vue   <-- Input Area Teks 6px Radius
│   │   ├── BaseCard.vue       <-- Kontainer 10px Radius Bergaris Rambut (Hairline 1px)
│   │   ├── BaseModal.vue      <-- Dialog 10px Radius (Escape & Backdrop Dismiss)
│   │   ├── BaseBadge.vue      <-- Lencana Teknis (Mono Uppercase, 0.06em Tracking)
│   │   ├── BaseToast.vue      <-- Toast Notifikasi Reaktif (Cobalt & Emerald Signals)
│   │   └── StatCard.vue       <-- Kartu Instrumen Metrik dengan Nilai Angka Tabular
│   └── Navigation/
│       ├── GlobalCommandPalette.vue <-- Modal ⌘K Command Palette Interaktif (Ciri Khas Cobalt)
│       └── BrandMark.vue      <-- Logo Teks Ramping "/paten" Bergaya Hallmark
```

---

## 3. Spesifikasi Komponen Kunci Hallmark Cobalt

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
  - `primary`: Latar belakang solid *Electric Cobalt* (`var(--color-cobalt)`), teks putih bersih, efek hover sedikit lebih gelap.
  - `secondary` / `outline`: Latar transparan, border 1px `var(--color-rule)`, teks `var(--color-ink-primary)`, hover border bergeser ke Cobalt.
  - `danger`: Teks merah ruby dengan border 1px lembut.
- **States:** Menampilkan spinner ramping ketika `loading: true` dan menonaktifkan klik.

### C. `BaseBadge.vue` (Monospace Technical Label)
- Menggunakan `font-mono` (`JetBrains Mono`), ukuran teks `0.75rem` (`text-xs`), teks UPPERCASE, dan `letter-spacing: 0.06em`.
- Memberikan impresi *machine-readout* instrumen teknologi terpercaya.

---

## 4. Alur Integrasi & Uji Coba

1. **Konfigurasi CSS:**
   - Memastikan `resources/css/app.css` mendefinisikan seluruh variabel OKLCH Cobalt dan utility typography.
2. **Setup Single Root Element:**
   - Seluruh layout dan komponen Vue membungkus markup dalam satu elemen root tunggal.
3. **Verifikasi Build:**
   - Menjalankan `npm run build` untuk memverifikasi seluruh komponen terkompilasi tanpa error.

---

## 5. Kriteria Penerimaan (Acceptance Criteria)
- [ ] Font Space Grotesk, Inter, dan JetBrains Mono termuat dengan baik.
- [ ] Token palet Cobalt OKLCH aktif dan diaplikasikan pada seluruh komponen dasar.
- [ ] Tersedia `GlobalCommandPalette.vue` yang aktif via klik dan shortcut keyboard `Ctrl+K` / `Cmd+K`.
- [ ] Seluruh tombol menggunakan radius teknis 6px tanpa bentuk pil bulat.
- [ ] `npm run build` sukses 100%.
