<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRundown;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CmsRundownController extends Controller
{
    /**
     * Store a newly created rundown session for the event.
     */
    public function store(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        /** @var Event $event */
        $event = $request->attributes->get('event') ?? Event::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'judul_sesi' => 'required|string|max:150',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after_or_equal:waktu_mulai',
            'pengisi_acara' => 'nullable|string|max:150',
            'urutan' => 'nullable|integer',
        ]);

        if (! isset($validated['urutan']) || $validated['urutan'] === null) {
            $maxUrutan = (int) $event->rundowns()->max('urutan');
            $validated['urutan'] = $maxUrutan + 1;
        }

        $validated['event_id'] = $event->id;

        $rundown = EventRundown::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sesi rundown berhasil ditambahkan.',
                'rundown' => $rundown,
            ], 201);
        }

        $token = $request->query('token') ?: $request->header('X-CMS-Token');

        return redirect()->to('/cms/'.$event->slug.'?token='.$token)
            ->with('success', 'Sesi rundown berhasil ditambahkan.');
    }

    /**
     * Update an existing rundown session.
     */
    public function update(Request $request, string $slug, string $id): JsonResponse|RedirectResponse
    {
        /** @var Event $event */
        $event = $request->attributes->get('event') ?? Event::where('slug', $slug)->firstOrFail();

        $rundown = $event->rundowns()->where('id', $id)->firstOrFail();

        $validated = $request->validate([
            'judul_sesi' => 'sometimes|required|string|max:150',
            'waktu_mulai' => 'sometimes|required|date',
            'waktu_selesai' => 'sometimes|required|date|after_or_equal:waktu_mulai',
            'pengisi_acara' => 'nullable|string|max:150',
            'urutan' => 'nullable|integer',
        ]);

        $rundown->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sesi rundown berhasil diperbarui.',
                'rundown' => $rundown,
            ]);
        }

        $token = $request->query('token') ?: $request->header('X-CMS-Token');

        return redirect()->to('/cms/'.$event->slug.'?token='.$token)
            ->with('success', 'Sesi rundown berhasil diperbarui.');
    }

    /**
     * Remove the specified rundown session.
     */
    public function destroy(Request $request, string $slug, string $id): JsonResponse|RedirectResponse
    {
        /** @var Event $event */
        $event = $request->attributes->get('event') ?? Event::where('slug', $slug)->firstOrFail();

        $rundown = $event->rundowns()->where('id', $id)->firstOrFail();
        $rundown->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sesi rundown berhasil dihapus.',
            ]);
        }

        $token = $request->query('token') ?: $request->header('X-CMS-Token');

        return redirect()->to('/cms/'.$event->slug.'?token='.$token)
            ->with('success', 'Sesi rundown berhasil dihapus.');
    }
}
