# Machine Learning & SPK Pipeline Configuration (Google Colab Ready)
**Proyek:** PATEN (Pemantauan Transaksi & Efektivitas Niaga) - Kabupaten Kutai Timur  
**Lingkungan Eksekusi:** Google Colaboratory (Python 3.10+) terhubung langsung ke Supabase PostgreSQL & REST API.

---

## 1. Arsitektur Pipeline ML + SPK Hybrid

Karena keputusan penyaluran bantuan pemerintah memiliki aturan hukum yang mengikat (Perbup Kutai Timur), sistem menggunakan **Arsitektur Hybrid 3-Tahap**:

1. **Tahap 1: Deterministic Regulatory Guardrail (Rule-Based Filter)**  
   Memeriksa syarat mutlak Perbup (Domisili Kutim, Bukan ASN/TNI/Polri, Validasi Duplikasi `no_kk` agar 1 KK hanya 1 kandidat utama, kepemilikan NIB $\ge 6$ bulan, dan belum pernah menerima Hibah Modal APBD).
2. **Tahap 2: Unsupervised Clustering (`K-Means`) & Multi-Criteria Scoring (`SAW`)**  
   Mengelompokkan performa riil UMKM selama event berdasarkan:
   * Frekuensi Transaksi ($X_1$)
   * Total Omzet ($X_2$)
   * Rasio Jam Aktif Operasional ($X_3$)
   * Rasio Transaksi Digital/QRIS ($X_4$)
   * Skor Legalitas ($X_5$)
3. **Tahap 3: Supervised Classification (`RandomForestClassifier`) & Decision Mapping**  
   Memetakan setiap UMKM ke dalam 1 dari 5 program intervensi resmi di Kutai Timur dan mengurutkan prioritas kuota 200 penerima.

---

## 2. Notebook Script Lengkap untuk Google Colab

Salin dan jalankan kode berikut secara berurutan di sel Google Colab untuk membuat dataset pelatihan sintetis khas Kutim, melatih model, mengekspor model (`.joblib`), dan menyinkronkan hasil prediksi ke tabel `spk_umkm_evaluations` di Supabase.

### Sel 1: Instalasi Dependensi di Google Colab
```python
!pip install -q supabase pandas numpy scikit-learn joblib psycopg2-binary
```

### Sel 2: Konfigurasi Koneksi Supabase & Generator Dataset Realistis Kutai Timur
```python
import os
import uuid
import joblib
import numpy as np
import pandas as pd
from sklearn.preprocessing import MinMaxScaler, StandardScaler
from sklearn.cluster import KMeans
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report, silhouette_score

# Set random seed untuk reproduksibilitas
np.random.seed(42)

def generate_kutim_umkm_dataset(n_samples=600):
    """
    Menghasilkan dataset sintetis performa stand UMKM saat event di Kutai Timur
    lengkap dengan atribut kepatuhan Perbup.
    """
    kecamatan_list = [
        'Sangatta Utara', 'Sangatta Selatan', 'Bengalon', 'Muara Wahau',
        'Kongbeng', 'Sangkulirang', 'Teluk Pandan', 'Rantau Pulung'
    ]
    
    data = []
    used_kk = set()
    
    for i in range(n_samples):
        # Simulasi duplikasi KK (sekitar 8% mencoba daftar ganda dalam 1 KK)
        if i > 20 and np.random.rand() < 0.08:
            no_kk = list(used_kk)[np.random.randint(0, len(used_kk))]
            is_duplicate_kk = True
        else:
            no_kk = f"6408{np.random.randint(100000000000, 999999999999)}"
            used_kk.add(no_kk)
            is_duplicate_kk = False
            
        is_domisili_kutim = np.random.choice([True, False], p=[0.95, 0.05])
        is_bukan_asn = np.random.choice([True, False], p=[0.96, 0.04])
        lama_nib_bulan = np.random.choice([0, 2, 4, 8, 14, 24, 36], p=[0.25, 0.10, 0.10, 0.20, 0.15, 0.10, 0.10])
        has_npwp = True if lama_nib_bulan >= 6 and np.random.rand() > 0.2 else False
        has_sku = np.random.choice([True, False], p=[0.85, 0.15])
        has_halal = np.random.choice([True, False], p=[0.45, 0.55])
        has_pirt = np.random.choice([True, False], p=[0.40, 0.60])
        has_hki = np.random.choice([True, False], p=[0.10, 0.90])
        pernah_terima_hibah_apbd = np.random.choice([True, False], p=[0.15, 0.85])
        
        # Simulasi Performa Transaksi POS selama Event (misal durasi 5 hari)
        frekuensi_transaksi = int(np.random.gamma(shape=2.5, scale=35))
        avg_ticket_size = np.random.choice([12000, 18000, 25000, 45000, 75000], p=[0.3, 0.3, 0.2, 0.15, 0.05])
        total_omzet = float(frekuensi_transaksi * avg_ticket_size)
        rasio_qris = np.round(np.random.beta(a=2, b=3), 4)
        rasio_jam_aktif = np.round(np.clip(np.random.normal(loc=0.75, scale=0.20), 0.10, 1.00), 4)
        
        # Skor kelengkapan legalitas (0 - 100)
        skor_legalitas = (
            (35 if lama_nib_bulan >= 6 else (15 if lama_nib_bulan > 0 else 0)) +
            (20 if has_npwp else 0) +
            (15 if has_sku else 0) +
            (15 if has_halal else 0) +
            (10 if has_pirt else 0) +
            (5 if has_hki else 0)
        )
        
        data.append({
            'umkm_id': str(uuid.uuid4()),
            'no_kk': no_kk,
            'is_duplicate_kk': is_duplicate_kk,
            'kecamatan': np.random.choice(kecamatan_list),
            'is_domisili_kutim': is_domisili_kutim,
            'is_bukan_asn': is_bukan_asn,
            'lama_nib_bulan': lama_nib_bulan,
            'has_npwp': has_npwp,
            'has_sku': has_sku,
            'has_halal': has_halal,
            'has_pirt': has_pirt,
            'has_hki': has_hki,
            'pernah_terima_hibah_apbd': pernah_terima_hibah_apbd,
            'frekuensi_transaksi': frekuensi_transaksi,
            'total_omzet': total_omzet,
            'rasio_qris': rasio_qris,
            'rasio_jam_aktif': rasio_jam_aktif,
            'skor_legalitas': float(skor_legalitas)
        })
        
    return pd.DataFrame(data)

df_raw = generate_kutim_umkm_dataset(600)
print(f"Dataset berhasil dibuat: {df_raw.shape[0]} baris.")
df_raw.head()
```

### Sel 3: Pipeline SPK Rule-Based Guardrail + K-Means Clustering + Random Forest
```python
def run_hybrid_spk_ml_pipeline(df):
    df = df.copy()
    
    # ---------------------------------------------------------
    # TAHAP 1: VALIDASI REGULASI PERBUP KUTIM (GUARDRAIL)
    # ---------------------------------------------------------
    # Urutkan berdasarkan frekuensi transaksi tertinggi agar jika ada KK ganda,
    # usaha yang paling aktif yang dipertahankan sebagai kandidat utama KK tersebut.
    df = df.sort_values(by=['frekuensi_transaksi', 'total_omzet'], ascending=False)
    df['kk_rank'] = df.groupby('no_kk').cumcount() + 1
    
    lolos_list = []
    alasan_list = []
    
    for _, row in df.iterrows():
        reasons = []
        if not row['is_domisili_kutim']:
            reasons.append("Bukan domisili Kutai Timur")
        if not row['is_bukan_asn']:
            reasons.append("Terdeteksi berstatus ASN/TNI/Polri/BUMN")
        if row['kk_rank'] > 1:
            reasons.append("Duplikasi Nomor KK (Melebihi batas 1 penerima per KK)")
        if row['lama_nib_bulan'] < 6:
            reasons.append("NIB belum aktif minimal 6 bulan / Belum punya NIB")
        if not row['has_npwp'] or not row['has_sku']:
            reasons.append("Dokumen NPWP atau SKU Kecamatan belum lengkap")
        if row['pernah_terima_hibah_apbd']:
            reasons.append("Sudah pernah menerima Hibah Modal APBD sebelumnya")
            
        if len(reasons) == 0:
            lolos_list.append(True)
            alasan_list.append("Memenuhi seluruh syarat Perbup Hibah UMKM Kutim")
        else:
            lolos_list.append(False)
            alasan_list.append("; ".join(reasons))
            
    df['is_lolos_syarat_perbup'] = lolos_list
    df['alasan_gagal_perbup'] = alasan_list
    
    # ---------------------------------------------------------
    # TAHAP 2: NORMALISASI FITUR & SKOR SPK (SAW)
    # ---------------------------------------------------------
    features = ['frekuensi_transaksi', 'total_omzet', 'rasio_jam_aktif', 'rasio_qris', 'skor_legalitas']
    scaler = MinMaxScaler()
    norm_matrix = scaler.fit_transform(df[features])
    df_norm = pd.DataFrame(norm_matrix, columns=[f"norm_{c}" for c in features], index=df.index)
    
    # Bobot sesuai PRD: Freq (0.30), Omzet (0.20), Jam Aktif (0.25), QRIS (0.10), Legalitas (0.15)
    weights = np.array([0.30, 0.20, 0.25, 0.10, 0.15])
    df['skor_akhir_spk'] = np.round(np.dot(norm_matrix, weights) * 100, 2)
    
    # ---------------------------------------------------------
    # TAHAP 3: K-MEANS CLUSTERING UNTUK PEMETAAN SKALA NIAGA
    # ---------------------------------------------------------
    kmeans = KMeans(n_clusters=3, random_state=42, n_init=10)
    raw_clusters = kmeans.fit_predict(norm_matrix[:, :4]) # Fokus pada 4 fitur performa transaksi
    
    # Urutkan indeks cluster berdasarkan rata-rata skor SPK (0: Mikro Awal, 1: Berkembang, 2: Mandiri)
    cluster_means = pd.Series(df['skor_akhir_spk']).groupby(raw_clusters).mean().sort_values().index
    cluster_map = {
        cluster_means[0]: 'Cluster_1_Mikro_Awal',
        cluster_means[1]: 'Cluster_2_Berkembang_Produktif',
        cluster_means[2]: 'Cluster_3_Mandiri_Ekspansif'
    }
    df['ml_cluster_label'] = [cluster_map[c] for c in raw_clusters]
    
    # ---------------------------------------------------------
    # TAHAP 4: PEMETAAN 5 PROGRAM BANTUAN UMKM KUTAI TIMUR
    # ---------------------------------------------------------
    recommendations = []
    for _, row in df.iterrows():
        # Jika gagal karena faktor fundamental (bukan warga Kutim, ASN, atau duplikat KK)
        if not row['is_domisili_kutim'] or not row['is_bukan_asn'] or row['kk_rank'] > 1:
            recommendations.append('unverified')
        # Jika aktif jualan tapi belum punya NIB >= 6 bulan atau belum lengkap Halal/PIRT
        elif row['lama_nib_bulan'] < 6 or not row['has_npwp']:
            recommendations.append('fasilitasi_legalitas_nib_halal')
        # Jika sudah pernah terima hibah APBD atau berada di Cluster 3 dengan omzet sangat tinggi -> Arahkan ke KUR/BPR/CSR
        elif row['pernah_terima_hibah_apbd'] or (row['ml_cluster_label'] == 'Cluster_3_Mandiri_Ekspansif' and row['total_omzet'] > 4500000):
            recommendations.append('kredit_bpr_bankaltimtara_csr')
        # Jika lolos syarat Perbup dan masuk Cluster 2 atau 3 -> Prioritas Hibah Modal APBD Rp 5-25 Juta
        elif row['is_lolos_syarat_perbup'] and row['ml_cluster_label'] in ['Cluster_2_Berkembang_Produktif', 'Cluster_3_Mandiri_Ekspansif']:
            recommendations.append('hibah_modal_apbd_5_25jt')
        # Jika frekuensi transaksi tinggi tapi omzet menengah/terkendala kapasitas -> Hibah Peralatan Produksi
        elif row['frekuensi_transaksi'] >= 70 and not row['is_lolos_syarat_perbup']:
            recommendations.append('hibah_peralatan_produksi')
        # Sisanya usaha mikro awal -> Bantuan BAZNAS / TKM Kemnaker Rp 5 Juta
        else:
            recommendations.append('bantuan_baznas_tkm_5jt')
            
    df['rekomendasi_program'] = recommendations
    
    # Latih RandomForestClassifier agar dapat digunakan untuk prediksi real-time via API
    rf_features = [
        'is_domisili_kutim', 'is_bukan_asn', 'lama_nib_bulan', 'has_npwp',
        'has_sku', 'pernah_terima_hibah_apbd', 'frekuensi_transaksi',
        'total_omzet', 'rasio_qris', 'rasio_jam_aktif', 'skor_legalitas', 'skor_akhir_spk'
    ]
    X = df[rf_features].astype(float)
    y = df['rekomendasi_program']
    
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42, stratify=y)
    rf_model = RandomForestClassifier(n_estimators=150, max_depth=10, random_state=42)
    rf_model.fit(X_train, y_train)
    
    print("=== EVALUASI MODEL KLASIFIKASI PROGRAM BANTUAN KUTIM ===")
    print(classification_report(y_test, rf_model.predict(X_test)))
    
    # Urutkan Peringkat Prioritas Event
    df = df.sort_values(by=['is_lolos_syarat_perbup', 'skor_akhir_spk'], ascending=[False, False])
    df['peringkat_prioritas_event'] = range(1, len(df) + 1)
    
    # Simpan artifacts model
    artifacts = {
        'scaler': scaler,
        'kmeans': kmeans,
        'cluster_map': cluster_map,
        'rf_model': rf_model,
        'rf_features': rf_features,
        'weights': weights
    }
    joblib.dump(artifacts, 'paten_kutim_ml_pipeline.joblib')
    print("Model berhasil disimpan ke: paten_kutim_ml_pipeline.joblib")
    
    return df, artifacts

df_evaluated, model_artifacts = run_hybrid_spk_ml_pipeline(df_raw)
df_evaluated[['umkm_id', 'skor_akhir_spk', 'ml_cluster_label', 'is_lolos_syarat_perbup', 'rekomendasi_program', 'peringkat_prioritas_event']].head(10)
```

### Sel 4: Script Sinkronisasi Langsung dari Google Colab ke Supabase Database
```python
from supabase import create_client, Client

# Isi dengan kredensial proyek Supabase Anda
SUPABASE_URL = os.environ.get("SUPABASE_URL", "https://YOUR_PROJECT_ID.supabase.co")
SUPABASE_SERVICE_KEY = os.environ.get("SUPABASE_SERVICE_KEY", "YOUR_SUPABASE_SERVICE_ROLE_KEY")

def sync_event_evaluations_in_supabase(event_id: str):
    """
    Menarik data profil UMKM dan transaksi POS dari Supabase untuk event tertentu,
    menjalankan pipeline SPK + ML, lalu meng-upsert hasilnya ke tabel spk_umkm_evaluations.
    """
    supabase: Client = create_client(SUPABASE_URL, SUPABASE_SERVICE_KEY)
    artifacts = joblib.load('paten_kutim_ml_pipeline.joblib')
    
    # 1. Ambil data stand beserta relasi umkm_profiles pada event_id terkait
    stands_res = supabase.table("event_stands").select("*, umkm_profiles(*)").eq("event_id", event_id).execute()
    stands_data = stands_res.data
    
    if not stands_data:
        print("Tidak ada stand terdaftar pada event ini.")
        return
        
    # 2. Ambil seluruh transaksi POS pada event ini
    tx_res = supabase.table("pos_transactions").select("*").eq("event_id", event_id).execute()
    df_tx = pd.DataFrame(tx_res.data)
    
    # 3. Agregasi fitur per stand
    records = []
    for stand in stands_data:
        umkm = stand.get("umkm_profiles")
        if not umkm:
            continue
            
        stand_id = stand["id"]
        if not df_tx.empty and "stand_id" in df_tx.columns:
            st_tx = df_tx[df_tx["stand_id"] == stand_id]
            freq = len(st_tx)
            omzet = float(st_tx["total_harga"].sum()) if freq > 0 else 0.0
            qris_count = len(st_tx[st_tx["metode_pembayaran"] == "qris"]) if freq > 0 else 0
            rasio_qris = round(qris_count / freq, 4) if freq > 0 else 0.0
            # Hitung jam unik aktif dibagi estimasi jam operasional event (misal 8 jam/hari)
            if freq > 0:
                active_hours = pd.to_datetime(st_tx["recorded_at"]).dt.floor("h").nunique()
                rasio_jam = min(round(active_hours / 16.0, 4), 1.0)
            else:
                rasio_jam = 0.0
        else:
            freq, omzet, rasio_qris, rasio_jam = 0, 0.0, 0.0, 0.0
            
        skor_leg = (
            (35 if (umkm.get("lama_nib_bulan") or 0) >= 6 else 0) +
            (20 if umkm.get("has_npwp") else 0) +
            (15 if umkm.get("has_sku_kecamatan") else 0) +
            (15 if umkm.get("has_sertifikat_halal") else 0) +
            (10 if umkm.get("has_pirt") else 0) +
            (5 if umkm.get("has_hki") else 0)
        )
        
        records.append({
            "event_id": event_id,
            "stand_id": stand_id,
            "umkm_id": umkm["id"],
            "no_kk": umkm["no_kk"],
            "is_domisili_kutim": umkm.get("is_domisili_kutim", True),
            "is_bukan_asn": umkm.get("is_bukan_asn_tni_polri", True),
            "lama_nib_bulan": umkm.get("lama_nib_bulan", 0),
            "has_npwp": umkm.get("has_npwp", False),
            "has_sku": umkm.get("has_sku_kecamatan", False),
            "pernah_terima_hibah_apbd": umkm.get("pernah_terima_hibah_apbd", False),
            "frekuensi_transaksi": freq,
            "total_omzet": omzet,
            "rasio_qris": rasio_qris,
            "rasio_jam_aktif": rasio_jam,
            "skor_legalitas": float(skor_leg)
        })
        
    df_event = pd.DataFrame(records)
    df_result, _ = run_hybrid_spk_ml_pipeline(df_event)
    
    # 4. Upsert ke tabel spk_umkm_evaluations
    upsert_payload = []
    for _, row in df_result.iterrows():
        upsert_payload.append({
            "event_id": row["event_id"],
            "stand_id": row["stand_id"],
            "umkm_id": row["umkm_id"],
            "is_lolos_syarat_perbup": bool(row["is_lolos_syarat_perbup"]),
            "alasan_gagal_perbup": row["alasan_gagal_perbup"],
            "frekuensi_transaksi": int(row["frekuensi_transaksi"]),
            "total_omzet_event": float(row["total_omzet"]),
            "rasio_transaksi_qris": float(row["rasio_qris"]),
            "rasio_jam_aktif": float(row["rasio_jam_aktif"]),
            "skor_kelengkapan_legalitas": float(row["skor_legalitas"]),
            "ml_cluster_label": row["ml_cluster_label"],
            "skor_akhir_spk": float(row["skor_akhir_spk"]),
            "rekomendasi_program": row["rekomendasi_program"],
            "peringkat_prioritas_event": int(row["peringkat_prioritas_event"])
        })
        
    supabase.table("spk_umkm_evaluations").upsert(upsert_payload, on_conflict="event_id,stand_id").execute()
    print(f"Sinkronisasi selesai! {len(upsert_payload)} stand UMKM telah dievaluasi.")
```