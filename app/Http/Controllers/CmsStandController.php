<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventStand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CmsStandController extends Controller
{
    /**
     * Batch or single provision event stands and assign unique 6-digit POS PINs.
     */
    public function provision(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        /** @var Event $event */
        $event = $request->attributes->get('event') ?? Event::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'jumlah_stand' => 'required|integer|min:1|max:100',
            'kategori_jualan' => 'required|in:kuliner,kriya,fesyen,agribisnis,jasa',
            'daya_listrik_watt' => 'required|integer|min:0',
            'fasilitas_detail' => 'nullable|array',
        ]);

        $count = (int) $validated['jumlah_stand'];
        $kategori = $validated['kategori_jualan'];
        $watt = (int) $validated['daya_listrik_watt'];
        $fasilitas = $validated['fasilitas_detail'] ?? ['meja' => 1, 'kursi' => 2];

        $createdStands = DB::transaction(function () use ($event, $count, $kategori, $watt, $fasilitas) {
            $existingCount = $event->stands()->count();
            $stands = [];
            $usedPins = $event->stands()->pluck('pin_pos')->toArray();

            for ($i = 1; $i <= $count; $i++) {
                $standNumber = sprintf('Stand %02d', $existingCount + $i);

                // Ensure unique 6-digit PIN within the event
                do {
                    $pin = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
                } while (in_array($pin, $usedPins, true));

                $usedPins[] = $pin;

                $stand = EventStand::create([
                    'event_id' => $event->id,
                    'nomor_stand' => $standNumber,
                    'kategori_jualan' => $kategori,
                    'daya_listrik_watt' => $watt,
                    'fasilitas_detail' => $fasilitas,
                    'pin_pos' => $pin,
                    'total_omzet_terekam' => 0.00,
                    'total_transaksi_terekam' => 0,
                ]);

                $stands[] = $stand;
            }

            return $stands;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => count($createdStands) . ' stand berhasil dibuat beserta PIN kasir.',
                'stands' => $createdStands,
            ], 201);
        }

        $token = $request->query('token') ?: $request->header('X-CMS-Token');
        return redirect()->to('/cms/' . $event->slug . '?token=' . $token)
            ->with('success', count($createdStands) . ' stand berhasil dibuat beserta PIN kasir.');
    }
}
