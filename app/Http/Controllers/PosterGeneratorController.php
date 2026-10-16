<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\QrCodeService;
use Illuminate\Http\JsonResponse;

class PosterGeneratorController extends Controller
{
    /**
     * Export poster metadata, theme colors, and public QR code.
     */
    public function export(string $slug, QrCodeService $qrCodeService): JsonResponse
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $publicUrl = url('/e/' . $event->slug);
        $qrSvg = $qrCodeService->generateSvg($publicUrl, 220);
        $qrDataUrl = $qrCodeService->generateDataUrl($publicUrl, 220);

        return response()->json([
            'success' => true,
            'poster' => [
                'nama_event' => $event->nama_event,
                'penyelenggara' => $event->penyelenggara,
                'lokasi_venue' => $event->lokasi_venue,
                'tanggal_mulai' => $event->tanggal_mulai?->format('d M Y H:i'),
                'tanggal_selesai' => $event->tanggal_selesai?->format('d M Y H:i'),
                'color_mode' => $event->color_mode,
                'color_primary' => $event->color_primary,
                'color_secondary' => $event->color_secondary,
                'gamifikasi_mode' => $event->gamifikasi_mode,
                'public_url' => $publicUrl,
                'qr_code_svg' => $qrSvg,
                'qr_code_data_url' => $qrDataUrl,
            ],
        ]);
    }
}
