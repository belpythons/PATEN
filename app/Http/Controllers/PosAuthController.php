<?php

namespace App\Http\Controllers;

use App\Models\EventStand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PosAuthController extends Controller
{
    /**
     * Show the POS cashier PIN login screen.
     */
    public function showLogin(Request $request, string $stand_id): InertiaResponse|JsonResponse
    {
        $stand = EventStand::with(['event', 'umkmProfile'])->findOrFail($stand_id);

        $data = [
            'stand' => $stand,
            'event' => $stand->event,
            'umkm' => $stand->umkmProfile,
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Pos/Login', $data);
    }

    /**
     * Authenticate POS cashier session using the 6-digit stand PIN.
     */
    public function login(Request $request, string $stand_id): JsonResponse|RedirectResponse
    {
        $stand = EventStand::findOrFail($stand_id);

        $validated = $request->validate([
            'pin_pos' => 'required|digits:6',
        ]);

        if (! hash_equals($stand->pin_pos, (string) $validated['pin_pos'])) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses kasir ditolak: PIN POS tidak valid.',
                ], 403);
            }

            return back()->withErrors(['pin_pos' => 'PIN POS yang Anda masukkan salah.']);
        }

        // Establish authorized cashier session
        $request->session()->put('pos_stand_id', $stand->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Autentikasi kasir berhasil.',
                'stand_id' => $stand->id,
                'redirect_url' => url('/pos/' . $stand->id),
            ], 200);
        }

        return redirect()->to('/pos/' . $stand->id)
            ->with('success', 'Selamat datang di terminal kasir ' . $stand->nomor_stand . '!');
    }
}
