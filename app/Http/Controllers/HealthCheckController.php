<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthCheckController extends Controller
{
    /**
     * Return comprehensive health check for Supabase DB, tables, and uuid extension.
     */
    public function index(): JsonResponse
    {
        $version = DB::scalar('SELECT version()');
        $uuid = DB::scalar('SELECT uuid_generate_v4()');

        $tables = [
            'events',
            'event_rundowns',
            'umkm_profiles',
            'event_stands',
            'stand_menus',
            'pos_transactions',
            'attendees',
            'spk_umkm_evaluations',
        ];

        $tableCounts = [];
        foreach ($tables as $t) {
            $tableCounts[$t] = DB::table($t)->count();
        }

        return response()->json([
            'status' => 'healthy',
            'database' => [
                'connection' => config('database.default'),
                'version' => $version,
                'tables_count' => count($tableCounts),
                'tables_status' => $tableCounts,
                'uuid_ossp' => $uuid ? 'active' : 'inactive',
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
