<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the centralized organizer & Dinas dashboard.
     */
    public function index(Request $request): Response|JsonResponse
    {
        $user = $request->user();

        $query = $user->role === 'admin_dinas'
            ? Event::query()
            : $user->events();

        $events = $query->withCount(['stands', 'attendees'])
            ->with(['stands' => fn ($q) => $q->select('id', 'event_id', 'nomor_stand', 'total_omzet_terekam', 'total_transaksi_terekam')])
            ->latest()
            ->get()
            ->makeVisible(['secure_management_token']);

        $totalEvents = $events->count();
        $totalStands = $events->sum('stands_count');
        $totalAttendees = $events->sum('attendees_count');
        $totalOmzet = $events->sum(function ($ev) {
            return (float) $ev->stands->sum('total_omzet_terekam');
        });
        $totalTransactions = $events->sum(function ($ev) {
            return (int) $ev->stands->sum('total_transaksi_terekam');
        });

        $metrics = [
            'total_events' => $totalEvents,
            'total_stands' => $totalStands,
            'total_attendees' => $totalAttendees,
            'total_omzet' => $totalOmzet,
            'total_transactions' => $totalTransactions,
        ];

        $data = [
            'user' => $user,
            'events' => $events,
            'metrics' => $metrics,
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Organizer/Dashboard', $data);
    }
}
