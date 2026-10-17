<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PosterGeneratorController extends Controller
{
    /**
     * Export poster metadata, theme colors, and public QR code.
     */
    public function export(Request $request, string $slug, QrCodeService $qrCodeService)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $publicUrl = url('/e/'.$event->slug);
        $qrSvg = $qrCodeService->generateSvg($publicUrl, 220);
        $qrDataUrl = $qrCodeService->generateDataUrl($publicUrl, 220);

        $posterData = [
            'nama_event' => $event->nama_event,
            'penyelenggara' => $event->penyelenggara,
            'lokasi_venue' => $event->lokasi_venue,
            'deskripsi' => $event->deskripsi,
            'tanggal_mulai' => $event->tanggal_mulai?->format('d M Y H:i'),
            'tanggal_selesai' => $event->tanggal_selesai?->format('d M Y H:i'),
            'color_mode' => $event->color_mode,
            'color_primary' => $event->color_primary,
            'color_secondary' => $event->color_secondary,
            'gamifikasi_mode' => $event->gamifikasi_mode,
            'public_url' => $publicUrl,
            'qr_code_svg' => $qrSvg,
            'qr_code_data_url' => $qrDataUrl,
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'poster' => $posterData,
            ]);
        }

        return Inertia::render('Events/PosterExport', [
            'poster' => $posterData,
            'event' => $event,
        ]);
    }
}
