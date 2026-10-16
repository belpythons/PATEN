<?php

namespace App\Http\Middleware;

use App\Models\EventStand;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyPosPinMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $standId = $request->route('stand_id');

        if (! $standId) {
            abort(400, 'Parameter stand_id tidak ditemukan pada rute.');
        }

        $stand = EventStand::find($standId);

        if (! $stand) {
            abort(404, 'Stand tidak ditemukan.');
        }

        $sessionStandId = $request->session()->get('pos_stand_id');
        $providedPin = $request->header('X-Stand-Pin')
            ?: $request->header('X-POS-PIN')
            ?: $request->input('pin_pos');

        $isAuthorized = false;

        if ($sessionStandId && $sessionStandId === $standId) {
            $isAuthorized = true;
        } elseif ($providedPin && hash_equals($stand->pin_pos, (string) $providedPin)) {
            $request->session()->put('pos_stand_id', $standId);
            $isAuthorized = true;
        }

        if (! $isAuthorized) {
            abort(403, 'Akses kasir ditolak: Sesi atau PIN POS tidak valid.');
        }

        // Attach resolved stand to request attributes
        $request->attributes->set('stand', $stand);

        return $next($request);
    }
}
