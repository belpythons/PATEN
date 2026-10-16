<?php

namespace App\Http\Controllers;

use App\Models\EventStand;
use App\Models\StandMenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class StandMenuController extends Controller
{
    /**
     * Store a new menu item for the stand.
     */
    public function store(Request $request, string $stand_id): JsonResponse|RedirectResponse
    {
        $stand = EventStand::findOrFail($stand_id);

        $validated = $request->validate([
            'nama_item' => 'required|string|max:150',
            'harga' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $menu = StandMenu::create([
            'stand_id' => $stand->id,
            'nama_item' => $validated['nama_item'],
            'harga' => $validated['harga'],
            'is_available' => $validated['is_available'] ?? true,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu ' . $menu->nama_item . ' berhasil ditambahkan.',
                'menu' => $menu,
            ], 201);
        }

        return redirect()->to('/pos/' . $stand->id)
            ->with('success', 'Menu ' . $menu->nama_item . ' berhasil ditambahkan.');
    }

    /**
     * Update an existing menu item.
     */
    public function update(Request $request, string $stand_id, string $menu_id): JsonResponse|RedirectResponse
    {
        $menu = StandMenu::where('id', $menu_id)
            ->where('stand_id', $stand_id)
            ->firstOrFail();

        $validated = $request->validate([
            'nama_item' => 'sometimes|required|string|max:150',
            'harga' => 'sometimes|required|numeric|min:0',
            'is_available' => 'sometimes|boolean',
        ]);

        $menu->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu ' . $menu->nama_item . ' berhasil diperbarui.',
                'menu' => $menu,
            ], 200);
        }

        return redirect()->to('/pos/' . $stand_id)
            ->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Remove or disable a menu item.
     */
    public function destroy(Request $request, string $stand_id, string $menu_id): JsonResponse|RedirectResponse
    {
        $menu = StandMenu::where('id', $menu_id)
            ->where('stand_id', $stand_id)
            ->firstOrFail();

        if ($menu->posTransactions()->exists()) {
            $menu->update(['is_available' => false]);
            $message = 'Menu memiliki riwayat transaksi, status diubah menjadi tidak tersedia.';
        } else {
            $menu->delete();
            $message = 'Menu berhasil dihapus.';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ], 200);
        }

        return redirect()->to('/pos/' . $stand_id)
            ->with('success', $message);
    }

    /**
     * Export stand menu sheet with primary/secondary event theme colors.
     */
    public function export(Request $request, string $stand_id): InertiaResponse|JsonResponse
    {
        $stand = EventStand::with(['event', 'umkmProfile', 'menus' => fn ($q) => $q->where('is_available', true)])
            ->findOrFail($stand_id);

        $data = [
            'stand' => [
                'id' => $stand->id,
                'nomor_stand' => $stand->nomor_stand,
                'kategori_jualan' => $stand->kategori_jualan,
                'nama_usaha' => $stand->umkmProfile?->nama_usaha ?? 'Stand UMKM',
                'nama_pemilik' => $stand->umkmProfile?->nama_pemilik ?? 'Pelaku Usaha',
            ],
            'event' => [
                'nama_event' => $stand->event->nama_event,
                'color_primary' => $stand->event->color_primary ?? '#1E3A8A',
                'color_secondary' => $stand->event->color_secondary ?? '#3B82F6',
            ],
            'menus' => $stand->menus,
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Pos/ExportMenu', $data);
    }
}
