<?php

namespace App\Http\Controllers;

use App\Models\EventStand;
use App\Models\SpkUmkmEvaluation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MlWebhookController extends Controller
{
    /**
     * Webhook receiver to sync machine learning clustering results from Colab/Python pipeline.
     */
    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|uuid|exists:events,id',
            'evaluations' => 'required|array',
            'evaluations.*.stand_id' => 'required|uuid|exists:event_stands,id',
            'evaluations.*.cluster_kategori' => 'required|string|max:50',
            'evaluations.*.rekomendasi_program' => 'required|in:pending,fasilitasi_legalitas,hibah_alat,hibah_modal_apbd,kredit_perbankan',
            'evaluations.*.skor_akhir_spk' => 'required|numeric',
        ]);

        $eventId = $validated['event_id'];
        $evaluations = $validated['evaluations'];

        $synced = DB::transaction(function () use ($eventId, $evaluations) {
            $updatedRecords = [];

            foreach ($evaluations as $item) {
                $stand = EventStand::find($item['stand_id']);
                if (! $stand || ! $stand->umkm_id) {
                    continue;
                }

                $record = SpkUmkmEvaluation::updateOrCreate(
                    [
                        'event_id' => $eventId,
                        'stand_id' => $item['stand_id'],
                    ],
                    [
                        'umkm_id' => $stand->umkm_id,
                        'cluster_kategori' => $item['cluster_kategori'],
                        'rekomendasi_program' => $item['rekomendasi_program'],
                        'skor_akhir_spk' => $item['skor_akhir_spk'],
                        'calculated_at' => now(),
                    ]
                );

                $updatedRecords[] = $record;
            }

            return $updatedRecords;
        });

        return response()->json([
            'success' => true,
            'message' => 'Sinkronisasi hasil machine learning berhasil diproses.',
            'synced_count' => count($synced),
        ], 200);
    }
}
