# Technical Staging Plan: STAGE 5
**Nama Tahapan:** Modul 4 – Hybrid SPK Engine, Perbup Guardrail 1 KK 1 Penerima, Google Colab ML Sync & Awarding Leaderboard  
**Target Modul:** Sistem Pendukung Keputusan (SPK) Berbasis Regulasi, Evaluasi Transaksi, Webhook Sinkronisasi ML, dan Klasemen Awarding  
**Standar Pengujian:** 100% PASS pada Automated Tests (Feature, Unit, Security)  

---

## 1. Skema Database & Relasi Eloquent Models

### A. Tabel Terlibat
1. **`spk_umkm_evaluations`**:
   - `event_id`, `umkm_id`, `stand_id`.
   - Fitur Metrik:
     * `score_konsistensi_transaksi`: Rasio jam/hari bertransaksi aktif ($w = 0.25$).
     * `score_digitalisasi_qris`: Rasio pembayaran QRIS ($w = 0.10$).
     * `score_kepatuhan_regulasi`: Skor kelengkapan NIB, Halal, PIRT, dan status KK ($w = 0.15$).
     * `skor_akhir_spk`: Nilai agregat SAW ($0.00 - 100.00$).
   - Klasifikasi & Rekomendasi:
     * `cluster_kategori`: Label klaster dari pipeline Machine Learning (misal: 'Micro Fast Moving', 'Established High Value').
     * `rekomendasi_program`: ENUM `spk_recommendation_enum` ('pending', 'fasilitasi_legalitas', 'hibah_alat', 'hibah_modal_apbd', 'kredit_perbankan').
     * `is_prioritas_hibah_200`: Flag kelayakan kuota prioritas 200 hibah modal APBD.
     * `catatan_dinas`: Penjelasan objektif alasan kelolosan atau diskualifikasi regulasi.

---

## 2. Logika Deterministic Guardrail & Formulasi Matematika SPK

### A. Guardrail Regulasi Perbup Kutim
1. **Aturan 1 KK = 1 Penerima:**
   - Sistem mengelompokkan kandidat berdasarkan atribut `no_kk`.
   - Jika terdapat $\ge 2$ UMKM dalam satu `no_kk`, hanya UMKM dengan skor performa tertinggi yang berhak masuk nominasi `hibah_modal_apbd`. UMKM lainnya dialihkan ke `fasilitasi_legalitas` atau `kredit_perbankan`.
2. **Aturan Legalitas & Hibah Sebelumnya:**
   - UMKM tanpa NIB atau masa NIB $< 6$ bulan otomatis diarahkan ke `fasilitasi_legalitas`.
   - UMKM dengan `pernah_terima_hibah == TRUE` didiskualifikasi dari `hibah_modal_apbd` (anti-duplikasi APBD).

### B. Formulasi Pembobotan SAW (*Simple Additive Weighting*)
$$\text{Skor Akhir} = (0.30 \cdot \hat{F}_{\text{freq}}) + (0.20 \cdot \hat{F}_{\text{omzet}}) + (0.25 \cdot \hat{F}_{\text{konsistensi}}) + (0.10 \cdot \hat{F}_{\text{qris}}) + (0.15 \cdot \hat{F}_{\text{legalitas}})$$

---

## 3. Routing, Controllers & Middleware

### A. Daftar Rute & Endpoint
| Method | URI | Controller & Action | Middleware | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| `POST` | `/cms/{slug}/evaluate-spk` | `SpkEvaluationController@evaluate` | `web`, `cms.token` | Trigger kalkulasi SPK & guardrail lokal |
| `GET` | `/cms/{slug}/evaluations` | `SpkEvaluationController@index` | `web`, `cms.token` | Laporan evaluasi lengkap + omzet riil |
| `POST` | `/api/ml/sync-evaluations` | `MlWebhookController@sync` | `api` | Webhook sinkronisasi hasil prediksi Colab/ML |
| `GET` | `/e/{slug}/leaderboard` | `PublicLeaderboardController@index` | `web` | Leaderboard publik (Top 1/3/5 tanpa omzet) |

---

## 4. Spesifikasi Payload & Proteksi Kebocoran Data (*Data Leak Guard*)

### A. Webhook Sinkronisasi ML (`POST /api/ml/sync-evaluations`)
- **Validation Rules:**
  - `event_id`: `required|uuid|exists:events,id`
  - `evaluations`: `required|array`
  - `evaluations.*.stand_id`: `required|uuid|exists:event_stands,id`
  - `evaluations.*.cluster_kategori`: `required|string`
  - `evaluations.*.rekomendasi_program`: `required|in:pending,fasilitasi_legalitas,hibah_alat,hibah_modal_apbd,kredit_perbankan`
  - `evaluations.*.skor_akhir_spk`: `required|numeric`

### B. Proteksi Visibilitas Data Publik (`GET /e/{slug}/leaderboard`)
- **JSON Response Publik:**
  ```json
  {
    "event": "Pekan Raya Kutim 2026",
    "awarding_tier": "top_3",
    "leaderboard": [
      {
        "peringkat": 1,
        "nomor_stand": "Stand 04",
        "nama_usaha": "Amplang Sangatta Prima",
        "poin_keaktifan": 850
      }
    ]
  }
  ```
  > [!CAUTION]
  > Field `total_omzet_terekam`, `nik`, `no_kk`, dan `nomor_wa` **DIHARAMKAN** muncul pada respons publik untuk melindungi privasi pedagang dari isu perpajakan dan persepsi persaingan tidak sehat.

---

## 5. Komponen Raw Frontend (Inertia Vue 3)

1. `resources/js/Pages/Events/Leaderboard.vue`:
   - `<h1>` Klasemen Awarding Keaktifan Stand.
   - `<table>` Peringkat, Nomor Stand, Nama Usaha, dan Poin Keaktifan.
2. `resources/js/Pages/Cms/Evaluations.vue`:
   - Tombol "Jalankan Evaluasi SPK & Guardrail Perbup".
   - `<table>` Rekapitulasi Lengkap: Nama Usaha, NIK, No KK, Omzet Riil, Status Lolos Perbup, Cluster ML, Rekomendasi Program Bantuan, dan Catatan Dinas.

---

## 6. Skenario Pengujian Otomatis (Wajib 100% PASS)

File pengujian: `tests/Feature/Stage5SpkEngineAndLeaderboardTest.php`
1. `test_regulatory_guardrail_prevents_duplicate_kk_receiving_apbd_grant`: Memastikan UMKM kedua dari KK yang sama otomatis dialihkan dan tidak mendapat `hibah_modal_apbd`.
2. `test_regulatory_guardrail_redirects_unverified_nib_to_legalitas_program`: UMKM dengan NIB kosong otomatis mendapat rekomendasi `fasilitasi_legalitas`.
3. `test_saw_scoring_mathematical_accuracy`: Memverifikasi perhitungan formula SAW sesuai matriks pembobotan.
4. `test_ml_sync_webhook_updates_evaluations_table`: Memastikan payload webhook dari script Python/Colab tersimpan dengan benar.
5. `test_public_leaderboard_masks_financial_and_identity_data`: Audit keamanan memverifikasi tidak ada kebocoran string rupiah, omzet, NIK, atau No KK pada respons `GET /e/{slug}/leaderboard`.
