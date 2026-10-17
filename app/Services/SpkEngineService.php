<?php

namespace App\Services;

use App\Models\Event;
use App\Models\SpkUmkmEvaluation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SpkEngineService
{
    /**
     * Weights configuration according to technical specification:
     * - Frequency (0.30)
     * - Omzet (0.20)
     * - Consistency (0.25)
     * - QRIS Digitalization (0.10)
     * - Legality / Regulatory Compliance (0.15)
     */
    public const WEIGHT_FREQ = 0.30;

    public const WEIGHT_OMZET = 0.20;

    public const WEIGHT_KONSISTENSI = 0.25;

    public const WEIGHT_QRIS = 0.10;

    public const WEIGHT_LEGALITAS = 0.15;

    /**
     * Execute comprehensive SAW evaluation with deterministic Perbup guardrails.
     *
     * @return Collection<int, SpkUmkmEvaluation>
     */
    public function evaluate(Event $event): Collection
    {
        $stands = $event->stands()
            ->whereNotNull('umkm_id')
            ->with(['umkmProfile', 'posTransactions'])
            ->get();

        if ($stands->isEmpty()) {
            return collect();
        }

        // 1. Determine maximum values for normalization
        $maxFreq = (float) $stands->max('total_transaksi_terekam');
        $maxOmzet = (float) $stands->max('total_omzet_terekam');

        $computedEvaluations = [];

        foreach ($stands as $stand) {
            $umkm = $stand->umkmProfile;

            // Frequency score (0 - 1.0)
            $freqScore = $maxFreq > 0 ? ((float) $stand->total_transaksi_terekam / $maxFreq) : 0.0;

            // Omzet score (0 - 1.0)
            $omzetScore = $maxOmzet > 0 ? ((float) $stand->total_omzet_terekam / $maxOmzet) : 0.0;

            // Consistency and QRIS scores based on active transactions or aggregate stats
            $transactions = $stand->posTransactions;
            $txCount = $transactions->count();

            if ($txCount > 0) {
                $uniqueHours = $transactions->map(fn ($t) => $t->created_at?->format('Y-m-d H'))
                    ->unique()
                    ->count();
                $konsistensiScore = min(1.0, round($uniqueHours / 8.0, 4));

                $qrisCount = $transactions->where('metode_pembayaran', 'qris')->count();
                $qrisScore = round($qrisCount / $txCount, 4);
            } elseif ($stand->total_transaksi_terekam > 0) {
                $konsistensiScore = min(1.0, round((float) $stand->total_transaksi_terekam / 20.0, 4));
                $qrisScore = 0.50; // Neutral baseline for pre-aggregated stands
            } else {
                $konsistensiScore = 0.0;
                $qrisScore = 0.0;
            }

            // Legality score (NIB: 0.50, Halal: 0.25, P-IRT: 0.25)
            $legalitasScore = 0.0;
            if ($umkm) {
                if (! empty($umkm->nib)) {
                    $legalitasScore += 0.50;
                }
                if ($umkm->has_halal) {
                    $legalitasScore += 0.25;
                }
                if ($umkm->has_pirt) {
                    $legalitasScore += 0.25;
                }
            }

            // SAW Final Aggregate Score (0 - 100 scale)
            $rawSaw = (self::WEIGHT_FREQ * $freqScore)
                + (self::WEIGHT_OMZET * $omzetScore)
                + (self::WEIGHT_KONSISTENSI * $konsistensiScore)
                + (self::WEIGHT_QRIS * $qrisScore)
                + (self::WEIGHT_LEGALITAS * $legalitasScore);

            $skorAkhir = round($rawSaw * 100, 2);

            // Initial program recommendation based on score
            $rekomendasi = 'fasilitasi_legalitas';
            $isPrioritas = false;
            $catatan = '';

            if ($skorAkhir >= 60.0) {
                $rekomendasi = 'hibah_modal_apbd';
                $isPrioritas = true;
                $catatan = 'Memenuhi syarat skor dan kriteria prioritas hibah modal APBD.';
            } elseif ($skorAkhir >= 40.0) {
                $rekomendasi = 'hibah_alat';
                $isPrioritas = false;
                $catatan = 'Memenuhi syarat bantuan hibah alat penunjang produksi.';
            } else {
                $rekomendasi = 'fasilitasi_legalitas';
                $isPrioritas = false;
                $catatan = 'Diarahkan ke pendampingan dan fasilitasi penguatan usaha.';
            }

            // Perbup Guardrail 1: Missing NIB
            if (! $umkm || empty($umkm->nib)) {
                $rekomendasi = 'fasilitasi_legalitas';
                $isPrioritas = false;
                $catatan = 'Dialihkan ke program fasilitasi legalitas karena belum memiliki NIB.';
            }

            // Perbup Guardrail 2: Prior Grant Recipient (Anti-duplikasi APBD)
            if ($umkm && $umkm->pernah_terima_hibah) {
                $rekomendasi = 'kredit_perbankan';
                $isPrioritas = false;
                $tahun = $umkm->tahun_terima_hibah ? ' tahun '.$umkm->tahun_terima_hibah : '';
                $catatan = 'Pernah menerima hibah'.$tahun.', dialihkan ke fasilitasi kredit perbankan.';
            }

            $computedEvaluations[] = [
                'event_id' => $event->id,
                'stand_id' => $stand->id,
                'umkm_id' => $umkm?->id,
                'no_kk' => $umkm?->no_kk,
                'nama_pemilik' => $umkm?->nama_pemilik,
                'score_konsistensi_transaksi' => $konsistensiScore,
                'score_digitalisasi_qris' => $qrisScore,
                'score_kepatuhan_regulasi' => $legalitasScore,
                'skor_akhir_spk' => $skorAkhir,
                'cluster_kategori' => $skorAkhir >= 70.0 ? 'Established High Value' : ($skorAkhir >= 40.0 ? 'Micro Fast Moving' : 'Emerging Artisan'),
                'rekomendasi_program' => $rekomendasi,
                'is_prioritas_hibah_200' => $isPrioritas,
                'catatan_dinas' => $catatan,
                'calculated_at' => now(),
            ];
        }

        // Perbup Guardrail 3: 1 KK = 1 Penerima Hibah Modal APBD
        $kkGroups = collect($computedEvaluations)->groupBy('no_kk');

        foreach ($kkGroups as $noKk => $items) {
            if (empty($noKk) || $items->count() <= 1) {
                continue;
            }

            // Filter items that qualify for hibah_modal_apbd
            $apbdCandidates = $items->where('rekomendasi_program', 'hibah_modal_apbd');

            if ($apbdCandidates->count() > 1) {
                // Find candidate with highest score
                $highestScoreItem = $apbdCandidates->sortByDesc('skor_akhir_spk')->first();

                foreach ($computedEvaluations as &$eval) {
                    if ((string) $eval['no_kk'] === (string) $noKk && $eval['stand_id'] !== $highestScoreItem['stand_id']) {
                        if ($eval['rekomendasi_program'] === 'hibah_modal_apbd') {
                            $eval['rekomendasi_program'] = 'kredit_perbankan';
                            $eval['is_prioritas_hibah_200'] = false;
                            $eval['catatan_dinas'] = 'Diskualifikasi hibah modal APBD: Terdeteksi kesamaan No KK dengan penerima prioritas lebih tinggi ('.$highestScoreItem['nama_pemilik'].'). Dialihkan ke kredit perbankan.';
                        }
                    }
                }
                unset($eval);
            }
        }

        // 3. Atomically persist evaluations to database
        return DB::transaction(function () use ($computedEvaluations) {
            $saved = collect();

            foreach ($computedEvaluations as $data) {
                if (! $data['umkm_id']) {
                    continue; // Skip stands without registered UMKM
                }

                $record = SpkUmkmEvaluation::updateOrCreate(
                    [
                        'event_id' => $data['event_id'],
                        'stand_id' => $data['stand_id'],
                    ],
                    [
                        'umkm_id' => $data['umkm_id'],
                        'score_konsistensi_transaksi' => $data['score_konsistensi_transaksi'],
                        'score_digitalisasi_qris' => $data['score_digitalisasi_qris'],
                        'score_kepatuhan_regulasi' => $data['score_kepatuhan_regulasi'],
                        'skor_akhir_spk' => $data['skor_akhir_spk'],
                        'cluster_kategori' => $data['cluster_kategori'],
                        'rekomendasi_program' => $data['rekomendasi_program'],
                        'is_prioritas_hibah_200' => $data['is_prioritas_hibah_200'],
                        'catatan_dinas' => $data['catatan_dinas'],
                        'calculated_at' => $data['calculated_at'],
                    ]
                );

                $saved->push($record);
            }

            return $saved;
        });
    }
}
