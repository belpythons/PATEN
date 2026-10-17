<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PublicLeaderboardController extends Controller
{
    /**
     * Display the public awarding leaderboard with strict financial & identity data leak masking.
     */
    public function index(Request $request, string $slug): InertiaResponse|JsonResponse
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // Retrieve top stands based on SPK score or transaction count
        $evaluations = $event->spkEvaluations()
            ->with(['stand.umkmProfile'])
            ->orderByDesc('skor_akhir_spk')
            ->limit(10)
            ->get();

        $leaderboard = [];
        $rank = 1;

        if ($evaluations->isNotEmpty()) {
            foreach ($evaluations as $eval) {
                $leaderboard[] = [
                    'peringkat' => $rank++,
                    'nomor_stand' => $eval->stand?->nomor_stand ?? 'Stand -',
                    'nama_usaha' => $eval->stand?->umkmProfile?->nama_usaha ?? 'Peserta UMKM',
                    'poin_keaktifan' => (int) round((float) $eval->skor_akhir_spk),
                ];
            }
        } else {
            // Fallback before SPK evaluation is run: rank by transaction volume
            $stands = $event->stands()
                ->with('umkmProfile')
                ->orderByDesc('total_transaksi_terekam')
                ->limit(10)
                ->get();

            foreach ($stands as $stand) {
                $leaderboard[] = [
                    'peringkat' => $rank++,
                    'nomor_stand' => $stand->nomor_stand,
                    'nama_usaha' => $stand->umkmProfile?->nama_usaha ?? 'Peserta UMKM',
                    'poin_keaktifan' => (int) ($stand->total_transaksi_terekam * 10),
                ];
            }
        }

        $data = [
            'event' => [
                'nama_event' => $event->nama_event,
                'slug' => $event->slug,
                'penyelenggara' => $event->penyelenggara,
                'lokasi_venue' => $event->lokasi_venue,
            ],
            'awarding_tier' => 'top_'.count($leaderboard),
            'leaderboard' => $leaderboard,
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Events/Leaderboard', $data);
    }
}
