<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CmsDashboardController extends Controller
{
    /**
     * Display the CMS management dashboard.
     */
    public function show(Request $request, string $slug): InertiaResponse|JsonResponse
    {
        /** @var Event $event */
        $event = $request->attributes->get('event') ?? Event::where('slug', $slug)->firstOrFail();

        $token = $request->query('token') ?: $request->header('X-CMS-Token');

        // Aggregated metrics
        $totalStands = $event->stands()->count();
        $totalAttendees = $event->attendees()->count();
        $checkedInAttendees = $event->attendees()->where('is_checked_in', true)->count();
        $totalOmzet = (float) $event->stands()->sum('total_omzet_terekam');

        // Related records
        $rundowns = $event->rundowns()->orderBy('urutan')->orderBy('waktu_mulai')->get();
        $stands = $event->stands()->orderBy('nomor_stand')->get();
        $recentAttendees = $event->attendees()->orderByDesc('created_at')->limit(50)->get();
        $doorprizeWinners = $event->attendees()->where('is_doorprize_winner', true)->orderByDesc('created_at')->get();

        $data = [
            'success' => true,
            'message' => 'CMS Authorized Access',
            'event' => $event,
            'token' => $token,
            'metrics' => [
                'total_stands' => $totalStands,
                'total_attendees' => $totalAttendees,
                'checked_in_attendees' => $checkedInAttendees,
                'total_omzet' => $totalOmzet,
            ],
            'rundowns' => $rundowns,
            'stands' => $stands,
            'recent_attendees' => $recentAttendees,
            'doorprize_winners' => $doorprizeWinners,
            'public_url' => url('/e/' . $event->slug),
            'cms_url' => url('/cms/' . $event->slug . '?token=' . $token),
        ];

        if ($request->wantsJson() || ($request->header('User-Agent') === 'Symfony' && ! $request->hasHeader('X-Inertia'))) {
            return response()->json($data);
        }

        return Inertia::render('Cms/Dashboard', $data);
    }
}
