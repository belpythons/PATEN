<?php

namespace App\Http\Controllers;

use App\Models\EventStand;
use App\Models\StandMenu;
use App\Models\UmkmProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class StandAssignmentController extends Controller
{
    /**
     * Show the stand assignment and Perbup compliance form.
     */
    public function create(Request $request, string $stand_id): InertiaResponse|JsonResponse
    {
        $stand = EventStand::with(['event', 'umkmProfile', 'menus'])->findOrFail($stand_id);

        $disclaimer = 'Pencatatan transaksi ini adalah portofolio verifikasi kurasi bantuan UMKM Kutim, BUKAN untuk dasar penarikan pajak daerah.';

        $data = [
            'stand' => $stand,
            'event' => $stand->event,
            'umkm' => $stand->umkmProfile,
            'disclaimer' => $disclaimer,
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Stands/Assign', $data);
    }

    /**
     * Process stand assignment with Perbup compliance attributes and initial menus.
     */
    public function assign(Request $request, string $stand_id): JsonResponse|RedirectResponse
    {
        $stand = EventStand::with('event')->findOrFail($stand_id);

        $validated = $request->validate([
            'nik' => 'required|digits:16',
            'no_kk' => 'required|digits:16',
            'nama_pemilik' => 'required|string|max:150',
            'nama_usaha' => 'required|string|max:150',
            'nomor_wa' => 'required|string|max:20',
            'kecamatan' => 'required|string|max:100',
            'alamat_usaha' => 'nullable|string',
            'nib' => 'nullable|string|max:50',
            'has_halal' => 'boolean',
            'has_pirt' => 'boolean',
            'pernah_terima_hibah' => 'boolean',
            'tahun_terima_hibah' => 'nullable|integer',
            'initial_menus' => 'nullable|array',
            'initial_menus.*.nama_item' => 'required_with:initial_menus|string|max:150',
            'initial_menus.*.harga' => 'required_with:initial_menus|numeric|min:0',
        ]);

        $umkm = DB::transaction(function () use ($stand, $validated) {
            $umkmData = [
                'no_kk' => $validated['no_kk'],
                'nama_pemilik' => $validated['nama_pemilik'],
                'nama_usaha' => $validated['nama_usaha'],
                'nomor_wa' => $validated['nomor_wa'],
                'kecamatan' => $validated['kecamatan'],
                'alamat_usaha' => $validated['alamat_usaha'] ?? null,
                'nib' => $validated['nib'] ?? null,
                'has_halal' => (bool) ($validated['has_halal'] ?? false),
                'has_pirt' => (bool) ($validated['has_pirt'] ?? false),
                'pernah_terima_hibah' => (bool) ($validated['pernah_terima_hibah'] ?? false),
                'tahun_terima_hibah' => isset($validated['tahun_terima_hibah']) ? (int) $validated['tahun_terima_hibah'] : null,
            ];

            $profile = UmkmProfile::updateOrCreate(
                ['nik' => $validated['nik']],
                $umkmData
            );

            $stand->update([
                'umkm_id' => $profile->id,
            ]);

            if (! empty($validated['initial_menus'])) {
                foreach ($validated['initial_menus'] as $menuItem) {
                    StandMenu::create([
                        'stand_id' => $stand->id,
                        'nama_item' => $menuItem['nama_item'],
                        'harga' => $menuItem['harga'],
                        'is_available' => true,
                    ]);
                }
            }

            return $profile;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil UMKM berhasil didaftarkan dan dihubungkan ke '.$stand->nomor_stand.'.',
                'umkm' => $umkm,
                'stand' => $stand->fresh(['umkmProfile', 'menus']),
            ], 200);
        }

        return redirect()->to('/pos/'.$stand->id.'/login')
            ->with('success', 'Registrasi UMKM berhasil! Silakan login kasir menggunakan PIN stand.');
    }
}
