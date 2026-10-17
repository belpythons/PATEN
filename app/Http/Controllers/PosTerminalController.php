<?php

namespace App\Http\Controllers;

use App\Models\EventStand;
use App\Models\PosTransaction;
use App\Models\StandMenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PosTerminalController extends Controller
{
    /**
     * Display the fast POS Mini terminal.
     */
    public function index(Request $request, string $stand_id): InertiaResponse|JsonResponse
    {
        /** @var EventStand $stand */
        $stand = $request->attributes->get('stand') ?? EventStand::findOrFail($stand_id);
        $stand->load([
            'event',
            'umkmProfile',
            'menus' => fn ($q) => $q->orderBy('nama_item'),
        ]);

        $recentTransactions = $stand->posTransactions()
            ->with('menu')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $data = [
            'stand' => $stand,
            'event' => $stand->event,
            'umkm' => $stand->umkmProfile,
            'menus' => $stand->menus,
            'recent_transactions' => $recentTransactions,
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Pos/Terminal', $data);
    }

    /**
     * Record a quick atomic transaction via POS Mini with lockForUpdate().
     */
    public function recordTransaction(Request $request, string $stand_id): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'menu_id' => 'required|uuid|exists:stand_menus,id',
            'qty' => 'required|integer|min:1|max:100',
            'metode_pembayaran' => 'required|in:cash,qris',
        ]);

        // Security check: ensure menu item belongs to this specific stand
        $menu = StandMenu::where('id', $validated['menu_id'])
            ->where('stand_id', $stand_id)
            ->first();

        if (! $menu) {
            return response()->json([
                'success' => false,
                'message' => 'Item menu tidak valid untuk stand ini.',
            ], 422);
        }

        $result = DB::transaction(function () use ($stand_id, $validated, $menu) {
            /** @var EventStand $stand */
            $stand = EventStand::where('id', $stand_id)
                ->lockForUpdate()
                ->firstOrFail();

            $qty = (int) $validated['qty'];
            $subtotal = round((float) $menu->harga * $qty, 2);

            $transaction = PosTransaction::create([
                'event_id' => $stand->event_id,
                'stand_id' => $stand->id,
                'menu_id' => $menu->id,
                'qty' => $qty,
                'subtotal' => $subtotal,
                'metode_pembayaran' => $validated['metode_pembayaran'],
            ]);

            $stand->total_omzet_terekam = round((float) $stand->total_omzet_terekam + $subtotal, 2);
            $stand->total_transaksi_terekam = (int) $stand->total_transaksi_terekam + 1;
            $stand->save();

            return [
                'transaction' => $transaction,
                'subtotal' => $subtotal,
                'total_omzet_stand' => $stand->total_omzet_terekam,
                'total_transaksi_stand' => $stand->total_transaksi_terekam,
            ];
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil direkam.',
                'subtotal' => $result['subtotal'],
                'total_omzet_stand' => $result['total_omzet_stand'],
                'total_transaksi_stand' => $result['total_transaksi_stand'],
                'transaction' => $result['transaction'],
            ], 201);
        }

        return redirect()->to('/pos/'.$stand_id)
            ->with('success', 'Transaksi Rp '.number_format($result['subtotal'], 0, ',', '.').' berhasil direkam.');
    }
}
