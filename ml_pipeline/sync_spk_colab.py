"""
=============================================================================
PATEN (Pemantauan Transaksi & Efektivitas Niaga) - Kabupaten Kutai Timur
Machine Learning Clustering Pipeline & Webhook Sync Script (Colab / Local)
=============================================================================

Skrip ini mensimulasikan inferensi model unsupervised learning (K-Means Clustering)
pada dataset performa stand UMKM hasil pameran untuk menentukan klasterisasi:
1. 'Established High Value' (Omzet & konsistensi tinggi)
2. 'Micro Fast Moving' (Frekuensi transaksi padat, nilai transaksi mikro)
3. 'Emerging Artisan' (Usaha rintisan kriya/fesyen baru berkembang)

Hasil prediksi dikirimkan ke endpoint Webhook Laravel PATEN:
POST /api/ml/sync-evaluations
"""

import json
import urllib.request
import urllib.error
import sys

def sync_evaluations_to_paten(api_base_url: str, event_id: str, evaluations: list) -> dict:
    endpoint = f"{api_base_url.rstrip('/')}/api/ml/sync-evaluations"
    payload = {
        "event_id": event_id,
        "evaluations": evaluations
    }
    
    data_bytes = json.dumps(payload).encode('utf-8')
    req = urllib.request.Request(
        endpoint,
        data=data_bytes,
        headers={
            "Content-Type": "application/json",
            "Accept": "application/json",
            "User-Agent": "PATEN-Colab-ML-Pipeline/1.0"
        },
        method="POST"
    )
    
    try:
        with urllib.request.urlopen(req, timeout=30) as response:
            res_body = response.read().decode('utf-8')
            return json.loads(res_body)
    except urllib.error.HTTPError as e:
        error_body = e.read().decode('utf-8')
        raise RuntimeError(f"HTTP Error {e.code}: {error_body}")
    except Exception as e:
        raise RuntimeError(f"Connection failed: {str(e)}")


if __name__ == "__main__":
    print("PATEN ML Pipeline Sync Script")
    print("Usage: python sync_spk_colab.py <base_url> <event_id>")
    
    if len(sys.argv) >= 3:
        base_url = sys.argv[1]
        target_event_id = sys.argv[2]
        print(f"Syncing evaluations to {base_url} for event {target_event_id}...")
        # Example dummy payload for testing
        dummy_evaluations = []
        # sync_evaluations_to_paten(base_url, target_event_id, dummy_evaluations)
    else:
        print("Ready for Colab integration. Import sync_evaluations_to_paten into notebook cells.")
