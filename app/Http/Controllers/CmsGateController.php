<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CmsGateController extends Controller
{
    /**
     * Check-in attendee at the gate using ticket/doorprize number.
     */
    public function checkIn(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        /** @var Event $event */
        $event = $request->attributes->get('event') ?? Event::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'nomor_tiket_doorprize' => 'required|string',
        ]);

        $ticketNumber = trim($validated['nomor_tiket_doorprize']);

        /** @var Attendee|null $attendee */
        $attendee = $event->attendees()
            ->where('nomor_tiket_doorprize', $ticketNumber)
            ->first();

        if (! $attendee) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan pada event ini.',
            ], 404);
        }

        if ($attendee->is_checked_in) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket ini sudah melakukan check-in sebelumnya pada ' . $attendee->check_in_time . '.',
                'attendee' => $attendee,
            ], 409);
        }

        $attendee->update([
            'is_checked_in' => true,
            'check_in_time' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil. Selamat datang di ' . $event->nama_event . '!',
                'attendee' => [
                    'id' => $attendee->id,
                    'nomor_tiket_doorprize' => $attendee->nomor_tiket_doorprize,
                    'nama_lengkap' => $attendee->nama_lengkap,
                    'tipe_registrasi' => $attendee->tipe_registrasi,
                    'check_in_time' => $attendee->check_in_time,
                ],
            ]);
        }

        $token = $request->query('token') ?: $request->header('X-CMS-Token');
        return redirect()->to('/cms/' . $event->slug . '?token=' . $token)
            ->with('success', 'Check-in berhasil untuk ' . $attendee->nama_lengkap);
    }

    /**
     * Draw random doorprize winners from checked-in attendees who haven't won yet.
     */
    public function drawDoorprize(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        /** @var Event $event */
        $event = $request->attributes->get('event') ?? Event::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'jumlah_pemenang' => 'required|integer|min:1|max:10',
        ]);

        $limit = (int) $validated['jumlah_pemenang'];

        $eligibleQuery = $event->attendees()
            ->where('is_checked_in', true)
            ->where('is_doorprize_winner', false);

        $eligibleCount = $eligibleQuery->count();

        if ($eligibleCount === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada peserta hadir yang memenuhi syarat untuk undian doorprize.',
            ], 422);
        }

        $winners = DB::transaction(function () use ($eligibleQuery, $limit) {
            $selected = $eligibleQuery->inRandomOrder()->limit($limit)->get();

            foreach ($selected as $winner) {
                $winner->update(['is_doorprize_winner' => true]);
            }

            return $selected;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengundi ' . count($winners) . ' pemenang doorprize.',
                'winners' => $winners,
            ]);
        }

        $token = $request->query('token') ?: $request->header('X-CMS-Token');
        return redirect()->to('/cms/' . $event->slug . '?token=' . $token)
            ->with('success', 'Berhasil mengundi ' . count($winners) . ' pemenang doorprize.');
    }
}
