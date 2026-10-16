<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\Event;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PublicEventController extends Controller
{
    /**
     * Show public landing page for the event.
     */
    public function show(string $slug, QrCodeService $qrCodeService)
    {
        $event = Event::where('slug', $slug)
            ->with(['rundowns' => fn ($q) => $q->orderBy('urutan')])
            ->firstOrFail();

        $eventUrl = url('/e/' . $event->slug);
        $qrCodeDataUrl = $qrCodeService->generateDataUrl($eventUrl, 160);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'event' => $event,
                'rundowns' => $event->rundowns,
                'public_url' => $eventUrl,
                'qr_code_data_url' => $qrCodeDataUrl,
            ]);
        }

        return Inertia::render('Events/Show', [
            'event' => $event,
            'rundowns' => $event->rundowns,
            'qrCodeUrl' => $qrCodeDataUrl,
        ]);
    }

    /**
     * Register visitor attendance (HTM ticket or doorprize coupon).
     */
    public function registerAttendance(Request $request, string $slug, QrCodeService $qrCodeService)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'nomor_wa' => 'required|string|max:20',
            'kecamatan' => 'required|string|max:100',
            'tipe_registrasi' => 'required|in:htm,doorprize',
        ]);

        $prefix = $validated['tipe_registrasi'] === 'htm' ? 'PATEN-HTM-' : 'PATEN-DOOR-';
        $ticketCode = $prefix . Str::upper(Str::random(8));

        // Ensure unique ticket number
        while (Attendee::where('nomor_tiket_doorprize', $ticketCode)->exists()) {
            $ticketCode = $prefix . Str::upper(Str::random(8));
        }

        $attendee = Attendee::create([
            'event_id' => $event->id,
            'nama_lengkap' => $validated['nama_lengkap'],
            'nomor_wa' => $validated['nomor_wa'],
            'kecamatan' => $validated['kecamatan'],
            'nomor_tiket_doorprize' => $ticketCode,
            'tipe_registrasi' => $validated['tipe_registrasi'],
            'is_checked_in' => false,
        ]);

        $qrDataUrl = $qrCodeService->generateDataUrl($ticketCode, 150);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran kehadiran berhasil.',
            'ticket' => [
                'id' => $attendee->id,
                'nomor_tiket_doorprize' => $attendee->nomor_tiket_doorprize,
                'nama_lengkap' => $attendee->nama_lengkap,
                'tipe_registrasi' => $attendee->tipe_registrasi,
                'qr_payload' => $attendee->nomor_tiket_doorprize,
                'qr_data_url' => $qrDataUrl,
            ],
        ], 201);
    }
}
