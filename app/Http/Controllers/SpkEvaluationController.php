<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\SpkEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SpkEvaluationController extends Controller
{
    /**
     * Trigger execution of the SAW SPK engine with Perbup guardrails.
     */
    public function evaluate(Request $request, string $slug, SpkEngineService $spkService): JsonResponse|RedirectResponse
    {
        /** @var Event $event */
        $event = $request->attributes->get('event') ?? Event::where('slug', $slug)->firstOrFail();

        $evaluations = $spkService->evaluate($event);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Evaluasi SPK dan verifikasi guardrail Perbup berhasil dieksekusi.',
                'total_evaluated' => $evaluations->count(),
                'evaluations' => $evaluations,
            ], 200);
        }

        $token = $request->query('token') ?: $request->header('X-CMS-Token');
        return redirect()->to('/cms/' . $event->slug . '/evaluations?token=' . $token)
            ->with('success', 'Evaluasi SPK berhasil dieksekusi untuk ' . $evaluations->count() . ' stand UMKM.');
    }

    /**
     * Display the full SPK evaluation and policy recommendation report.
     */
    public function index(Request $request, string $slug): InertiaResponse|JsonResponse
    {
        /** @var Event $event */
        $event = $request->attributes->get('event') ?? Event::where('slug', $slug)->firstOrFail();

        $token = $request->query('token') ?: $request->header('X-CMS-Token');

        $evaluations = $event->spkEvaluations()
            ->with(['stand', 'umkmProfile'])
            ->orderByDesc('skor_akhir_spk')
            ->get();

        $data = [
            'event' => $event,
            'token' => $token,
            'evaluations' => $evaluations,
            'summary' => [
                'total_evaluated' => $evaluations->count(),
                'prioritas_hibah_200' => $evaluations->where('is_prioritas_hibah_200', true)->count(),
                'fasilitasi_legalitas' => $evaluations->where('rekomendasi_program', 'fasilitasi_legalitas')->count(),
                'hibah_alat' => $evaluations->where('rekomendasi_program', 'hibah_alat')->count(),
                'kredit_perbankan' => $evaluations->where('rekomendasi_program', 'kredit_perbankan')->count(),
            ],
            'cms_url' => url('/cms/' . $event->slug . '?token=' . $token),
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Cms/Evaluations', $data);
    }
}
