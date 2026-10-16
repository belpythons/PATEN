<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRundown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    /**
     * Show the event creation form.
     */
    public function create(): Response
    {
        return Inertia::render('Events/Create', [
            'defaultPrimaryColor' => '#0F766E',
            'defaultSecondaryColor' => '#F59E0B',
            'gamifikasiOptions' => [
                ['value' => 'none', 'label' => 'Tanpa Tiket & Doorprize'],
                ['value' => 'htm', 'label' => 'Mode A: Tiket HTM Berbayar'],
                ['value' => 'doorprize', 'label' => 'Mode B: Absensi Kupon Doorprize (Event Gratis)'],
                ['value' => 'both', 'label' => 'Kombinasi HTM & Doorprize'],
            ],
        ]);
    }

    /**
     * Store a newly created event with initial rundowns in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_event' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'penyelenggara' => 'required|string|max:150',
            'lokasi_venue' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'color_mode' => 'required|in:mono,duo',
            'color_primary' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_secondary' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'gamifikasi_mode' => 'required|in:none,htm,doorprize,both',
            'harga_htm' => 'nullable|numeric|min:0',
            'is_awarding_active' => 'boolean',
            'initial_rundowns' => 'nullable|array',
            'initial_rundowns.*.judul_sesi' => 'required|string|max:200',
            'initial_rundowns.*.waktu_mulai' => 'required|date',
            'initial_rundowns.*.waktu_selesai' => 'required|date',
            'initial_rundowns.*.pengisi_acara' => 'nullable|string|max:150',
            'initial_rundowns.*.deskripsi_sesi' => 'nullable|string',
        ]);

        // Generate clean unique slug
        $baseSlug = Str::slug($validated['nama_event']);
        $slug = $baseSlug;
        if (Event::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . Str::lower(Str::random(5));
        }

        // Generate 64-char cryptographic token
        $secureToken = Str::random(64);

        $event = DB::transaction(function () use ($validated, $slug, $secureToken) {
            $event = Event::create([
                'slug' => $slug,
                'nama_event' => $validated['nama_event'],
                'deskripsi' => $validated['deskripsi'],
                'penyelenggara' => $validated['penyelenggara'],
                'lokasi_venue' => $validated['lokasi_venue'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'secure_management_token' => $secureToken,
                'template_theme' => 'kutim_formal',
                'color_mode' => $validated['color_mode'],
                'color_primary' => $validated['color_primary'],
                'color_secondary' => $validated['color_mode'] === 'duo' ? ($validated['color_secondary'] ?? '#F59E0B') : $validated['color_primary'],
                'gamifikasi_mode' => $validated['gamifikasi_mode'],
                'harga_htm' => in_array($validated['gamifikasi_mode'], ['htm', 'both']) ? ($validated['harga_htm'] ?? 0.00) : 0.00,
                'is_awarding_active' => $validated['is_awarding_active'] ?? true,
            ]);

            // Save initial rundowns if provided
            if (! empty($validated['initial_rundowns'])) {
                foreach ($validated['initial_rundowns'] as $index => $item) {
                    EventRundown::create([
                        'event_id' => $event->id,
                        'waktu_mulai' => $item['waktu_mulai'],
                        'waktu_selesai' => $item['waktu_selesai'],
                        'judul_sesi' => $item['judul_sesi'],
                        'pengisi_acara' => $item['pengisi_acara'] ?? null,
                        'deskripsi_sesi' => $item['deskripsi_sesi'] ?? null,
                        'urutan' => $index + 1,
                    ]);
                }
            }

            return $event;
        });

        $publicUrl = url('/e/' . $event->slug);
        $cmsManageUrl = url('/cms/' . $event->slug . '?token=' . $event->secure_management_token);

        // Hybrid response: JSON for API / Testing, Inertia redirect for web form
        if ($request->wantsJson() || ! $request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'Event berhasil dibuat.',
                'data' => [
                    'id' => $event->id,
                    'slug' => $event->slug,
                    'public_url' => $publicUrl,
                    'cms_manage_url' => $cmsManageUrl,
                    'secure_management_token' => $event->secure_management_token,
                ],
            ], 201);
        }

        return redirect()->away($cmsManageUrl);
    }
}
