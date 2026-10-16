<?php

namespace Tests\Feature;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\EventRundown;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class Stage2EventCreationAndPublicGamificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\RateLimiter::clear(md5('attendance' . '127.0.0.1'));
    }

    /**
     * Test event creation validation rules.
     */
    public function test_event_creation_validates_required_fields(): void
    {
        $response = $this->postJson('/events', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'nama_event',
                'deskripsi',
                'penyelenggara',
                'lokasi_venue',
                'tanggal_mulai',
                'tanggal_selesai',
                'color_mode',
                'color_primary',
                'gamifikasi_mode',
            ]);
    }

    /**
     * Test event creation generates unique slug and 64-char token.
     */
    public function test_event_creation_generates_unique_slug_and_secure_token(): void
    {
        $payload = [
            'nama_event' => 'Gebyar UMKM Sangatta Utara 2026',
            'deskripsi' => 'Pameran produk unggulan UMKM binaan Diskop UKM Kutim',
            'penyelenggara' => 'Diskop UKM Kutim & PT KPC',
            'lokasi_venue' => 'Alun-Alun Sangatta',
            'tanggal_mulai' => now()->addDays(5)->toDateTimeString(),
            'tanggal_selesai' => now()->addDays(8)->toDateTimeString(),
            'color_mode' => 'duo',
            'color_primary' => '#0F766E',
            'color_secondary' => '#F59E0B',
            'gamifikasi_mode' => 'doorprize',
            'is_awarding_active' => true,
        ];

        $response = $this->postJson('/events', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'slug',
                    'public_url',
                    'cms_manage_url',
                    'secure_management_token',
                ],
            ]);

        $data = $response->json('data');
        $this->assertEquals(64, strlen($data['secure_management_token']));
        $this->assertStringContainsString('gebyar-umkm-sangatta-utara-2026', $data['slug']);
        $this->assertDatabaseHas('events', [
            'id' => $data['id'],
            'secure_management_token' => $data['secure_management_token'],
        ]);

        // Cleanup
        Event::destroy($data['id']);
    }

    /**
     * Test event creation saves initial rundowns inside a transaction.
     */
    public function test_event_creation_saves_initial_rundowns_transactionally(): void
    {
        $payload = [
            'nama_event' => 'Bazar Kuliner Nusantara Kutim',
            'deskripsi' => 'Festival kuliner daerah khas Dayak dan Kutai',
            'penyelenggara' => 'Panitia HUT Kutim',
            'lokasi_venue' => 'Polder Ilham Maulana',
            'tanggal_mulai' => now()->addDays(2)->toDateTimeString(),
            'tanggal_selesai' => now()->addDays(4)->toDateTimeString(),
            'color_mode' => 'mono',
            'color_primary' => '#1E3A8A',
            'gamifikasi_mode' => 'none',
            'initial_rundowns' => [
                [
                    'judul_sesi' => 'Registrasi Ulang Stand Kuliner',
                    'waktu_mulai' => now()->addDays(2)->setTime(8, 0)->toDateTimeString(),
                    'waktu_selesai' => now()->addDays(2)->setTime(9, 0)->toDateTimeString(),
                    'pengisi_acara' => 'Sekretariat Panitia',
                ],
                [
                    'judul_sesi' => 'Seremonial Pembukaan & Sambutan',
                    'waktu_mulai' => now()->addDays(2)->setTime(9, 30)->toDateTimeString(),
                    'waktu_selesai' => now()->addDays(2)->setTime(11, 0)->toDateTimeString(),
                    'pengisi_acara' => 'Bupati Kutai Timur',
                ],
            ],
        ];

        $response = $this->postJson('/events', $payload);
        $response->assertStatus(201);

        $eventId = $response->json('data.id');
        $this->assertEquals(2, EventRundown::where('event_id', $eventId)->count());

        // Cleanup cascade
        Event::destroy($eventId);
    }

    /**
     * Test public event page renders with event information and rundown schedule.
     */
    public function test_public_event_page_renders_with_event_data_and_rundown(): void
    {
        $event = Event::create([
            'slug' => 'public-test-' . Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Raya Sangkulirang 2026',
            'deskripsi' => 'Pameran hasil laut dan kerajinan',
            'penyelenggara' => 'Kecamatan Sangkulirang',
            'lokasi_venue' => 'Dermaga Sangkulirang',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => Str::random(64),
            'gamifikasi_mode' => 'doorprize',
        ]);

        EventRundown::create([
            'event_id' => $event->id,
            'judul_sesi' => 'Lomba Perahu Tradisional',
            'waktu_mulai' => now()->addDays(1)->setTime(14, 0),
            'waktu_selesai' => now()->addDays(1)->setTime(16, 0),
            'urutan' => 1,
        ]);

        $response = $this->getJson('/e/' . $event->slug);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'event' => [
                    'id' => $event->id,
                    'slug' => $event->slug,
                    'nama_event' => 'Pekan Raya Sangkulirang 2026',
                ],
            ])
            ->assertJsonCount(1, 'rundowns');

        // Cleanup
        $event->delete();
    }

    /**
     * Test public attendance registration creates unique ticket with QR data.
     */
    public function test_public_attendance_registration_creates_unique_ticket(): void
    {
        $event = Event::create([
            'slug' => 'doorprize-event-' . Str::lower(Str::random(6)),
            'nama_event' => 'Gebyar Doorprize Kutim',
            'deskripsi' => 'Undian gratis',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
            'gamifikasi_mode' => 'doorprize',
        ]);

        $payload = [
            'nama_lengkap' => 'Rizky Pratama',
            'nomor_wa' => '082155551234',
            'kecamatan' => 'Sangatta Utara',
            'tipe_registrasi' => 'doorprize',
        ];

        $response = $this->postJson('/e/' . $event->slug . '/register-attendance', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'ticket' => [
                    'id',
                    'nomor_tiket_doorprize',
                    'nama_lengkap',
                    'tipe_registrasi',
                    'qr_payload',
                    'qr_data_url',
                ],
            ]);

        $ticket = $response->json('ticket');
        $this->assertStringStartsWith('PATEN-DOOR-', $ticket['nomor_tiket_doorprize']);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $ticket['qr_data_url']);

        $this->assertDatabaseHas('attendees', [
            'event_id' => $event->id,
            'nomor_tiket_doorprize' => $ticket['nomor_tiket_doorprize'],
            'is_checked_in' => false,
        ]);

        // Cleanup
        $event->delete();
    }

    /**
     * Test attendance registration rate limiting (throttle 30 req/min).
     */
    public function test_attendance_registration_rate_limiting(): void
    {
        $event = Event::create([
            'slug' => 'throttle-test-' . Str::lower(Str::random(6)),
            'nama_event' => 'Throttle Test Event',
            'deskripsi' => 'Test',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
            'gamifikasi_mode' => 'doorprize',
        ]);

        // Pre-seed rate limiter with 30 hits on client IP to verify 429 on 31st request
        $key = md5('attendance' . '127.0.0.1');
        for ($i = 0; $i < 30; $i++) {
            \Illuminate\Support\Facades\RateLimiter::hit($key, 60);
        }

        $resp = $this->postJson('/e/' . $event->slug . '/register-attendance', [
            'nama_lengkap' => 'Overlimit Visitor',
            'nomor_wa' => '081299998888',
            'kecamatan' => 'Sangatta Utara',
            'tipe_registrasi' => 'doorprize',
        ]);

        $this->assertEquals(429, $resp->status());

        // Clear rate limiter for subsequent tests
        \Illuminate\Support\Facades\RateLimiter::clear($key);

        // Cleanup
        $event->delete();
    }

    /**
     * Test poster export endpoint returns valid theme colors, metadata, and SVG QR code.
     */
    public function test_poster_export_endpoint_returns_valid_data(): void
    {
        $event = Event::create([
            'slug' => 'poster-test-' . Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Budaya Sangatta 2026',
            'deskripsi' => 'Pentas seni',
            'penyelenggara' => 'Disporapar Kutim',
            'lokasi_venue' => 'Taman Bersemi',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => Str::random(64),
            'color_mode' => 'duo',
            'color_primary' => '#0F766E',
            'color_secondary' => '#F59E0B',
        ]);

        $response = $this->getJson('/events/' . $event->slug . '/poster-export');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'poster' => [
                    'nama_event' => 'Pekan Budaya Sangatta 2026',
                    'color_primary' => '#0F766E',
                    'color_secondary' => '#F59E0B',
                    'public_url' => url('/e/' . $event->slug),
                ],
            ]);

        $poster = $response->json('poster');
        $this->assertStringContainsString('<svg', $poster['qr_code_svg']);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $poster['qr_code_data_url']);

        // Cleanup
        $event->delete();
    }

    /**
     * Test Inertia web form submission redirects directly to CMS URL with token.
     */
    public function test_event_creation_inertia_form_redirects_to_cms(): void
    {
        $randomSuffix = Str::lower(Str::random(5));
        $eventName = 'Expo Usaha Kreatif ' . $randomSuffix;
        $payload = [
            'nama_event' => $eventName,
            'deskripsi' => 'Pameran kriya dan fesyen',
            'penyelenggara' => 'Dekranasda Kutim',
            'lokasi_venue' => 'Gedung Wanita',
            'tanggal_mulai' => now()->addDays(4)->toDateTimeString(),
            'tanggal_selesai' => now()->addDays(6)->toDateTimeString(),
            'color_mode' => 'mono',
            'color_primary' => '#047857',
            'gamifikasi_mode' => 'doorprize',
        ];

        $response = $this->withHeaders(['X-Inertia' => 'true'])
            ->post('/events', $payload);

        $response->assertStatus(302);
        $redirectUrl = $response->headers->get('Location');
        $this->assertStringContainsString('/cms/expo-usaha-kreatif-' . $randomSuffix . '?token=', $redirectUrl);

        // Cleanup
        Event::where('nama_event', $eventName)->delete();
    }
}
