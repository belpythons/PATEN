<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCmsTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('slug');

        if (! $slug) {
            abort(400, 'Parameter slug event tidak ditemukan pada rute.');
        }

        $event = Event::where('slug', $slug)->first();

        if (! $event) {
            abort(404, 'Event tidak ditemukan.');
        }

        $token = $request->query('token') ?: $request->header('X-CMS-Token');

        if (! $token) {
            abort(401, 'Token rahasia CMS diperlukan untuk mengakses halaman ini.');
        }

        if (! hash_equals($event->secure_management_token, (string) $token)) {
            abort(403, 'Akses CMS ditolak: Token rahasia tidak valid.');
        }

        // Attach resolved event to request attributes
        $request->attributes->set('event', $event);

        return $next($request);
    }
}
