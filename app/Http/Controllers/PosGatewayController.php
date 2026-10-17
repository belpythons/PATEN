<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventStand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PosGatewayController extends Controller
{
    /**
     * Display the centralized POS cashier gateway.
     */
    public function index(Request $request): Response|JsonResponse
    {
        $events = Event::with(['stands' => function ($q) {
            $q->select('id', 'event_id', 'umkm_id', 'nomor_stand', 'kategori_jualan')
                ->with(['umkmProfile' => function ($uq) {
                    $uq->select('id', 'nama_usaha', 'nama_pemilik');
                }])
                ->orderBy('nomor_stand');
        }])
            ->latest()
            ->get(['id', 'slug', 'nama_event', 'penyelenggara', 'lokasi_venue', 'tanggal_mulai', 'tanggal_selesai']);

        $data = [
            'events' => $events,
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Pos/Gateway', $data);
    }

    /**
     * Authenticate stand cashier via POS Gateway.
     */
    public function authenticate(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'stand_id' => 'required|uuid|exists:event_stands,id',
            'pin_pos' => 'required|digits:6',
        ]);

        $stand = EventStand::findOrFail($validated['stand_id']);

        if (! hash_equals($stand->pin_pos, (string) $validated['pin_pos'])) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'PIN POS yang Anda masukkan salah.',
                ], 403);
            }

            return back()->withErrors(['pin_pos' => 'PIN POS yang Anda masukkan salah.']);
        }

        // Establish authorized cashier session
        $request->session()->put('pos_stand_id', $stand->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Login kasir stand berhasil.',
                'terminal_url' => url('/pos/'.$stand->id),
            ]);
        }

        return redirect()->to('/pos/'.$stand->id);
    }
}
