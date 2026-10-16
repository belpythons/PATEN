<?php

namespace Tests\Feature;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\EventRundown;
use App\Models\EventStand;
use App\Models\PosTransaction;
use App\Models\SpkUmkmEvaluation;
use App\Models\StandMenu;
use App\Models\UmkmProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class Stage1CoreInfrastructureTest extends TestCase
{
    /**
     * Test health check endpoint returns 200 and healthy database status.
     */
    public function test_health_check_endpoint_returns_healthy_status(): void
    {
        $response = $this->getJson('/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'healthy',
                'database' => [
                    'connection' => 'pgsql',
                    'tables_count' => 8,
                    'uuid_ossp' => 'active',
                ],
            ])
            ->assertJsonStructure([
                'status',
                'database' => [
                    'connection',
                    'version',
                    'tables_count',
                    'tables_status' => [
                        'events',
                        'event_rundowns',
                        'umkm_profiles',
                        'event_stands',
                        'stand_menus',
                        'pos_transactions',
                        'attendees',
                        'spk_umkm_evaluations',
                    ],
                    'uuid_ossp',
                ],
                'timestamp',
            ]);
    }

    /**
     * Test Supabase connection and all 8 core tables exist and are queryable.
     */
    public function test_supabase_connection_and_table_schema_integrity(): void
    {
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

        foreach ($tables as $t) {
            $count = DB::table($t)->count();
            $this->assertGreaterThanOrEqual(0, $count, "Table [{$t}] is queryable");
        }
    }

    /**
     * Test uuid_generate_v4() extension generates valid UUID v4 string.
     */
    public function test_uuid_generation_works_seamlessly(): void
    {
        $uuid = DB::scalar('SELECT uuid_generate_v4()');
        $this->assertNotNull($uuid);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            (string) $uuid
        );
    }

    /**
     * Test Eloquent models instantiate with HasUuids and establish correct relations.
     */
    public function test_eloquent_models_can_instantiate_and_relate(): void
    {
        $testSlug = 'stage1-test-event-' . Str::lower(Str::random(8));
        $testToken = Str::random(64);

        // 1. Create Event
        $event = Event::create([
            'slug' => $testSlug,
            'nama_event' => 'Uji Coba Pameran Kutim Stage 1',
            'deskripsi' => 'Deskripsi event uji coba infrastruktur inti',
            'penyelenggara' => 'Diskop UKM Kutai Timur',
            'lokasi_venue' => 'Gedung Expo Sangatta',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => $testToken,
            'template_theme' => 'kutim_formal',
            'color_mode' => 'duo',
            'color_primary' => '#0F766E',
            'color_secondary' => '#F59E0B',
            'gamifikasi_mode' => 'doorprize',
            'harga_htm' => 0.00,
            'is_awarding_active' => true,
        ]);

        $this->assertNotEmpty($event->id);
        $this->assertTrue(Str::isUuid($event->id));

        // 2. Create Rundown
        $rundown = EventRundown::create([
            'event_id' => $event->id,
            'waktu_mulai' => now()->addDays(1)->setTime(9, 0),
            'waktu_selesai' => now()->addDays(1)->setTime(10, 0),
            'judul_sesi' => 'Pembukaan Bazar Gebyar UMKM',
            'pengisi_acara' => 'Bupati Kutai Timur',
            'urutan' => 1,
        ]);
        $this->assertEquals($event->id, $rundown->event->id);

        // 3. Create UmkmProfile
        $nik = '6408' . str_pad((string) mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
        $umkm = UmkmProfile::create([
            'nik' => $nik,
            'no_kk' => '6408123456789012',
            'nama_pemilik' => 'Ibu Siti Rahma',
            'nama_usaha' => 'Amplang Sangatta Berkah',
            'nomor_wa' => '081234567890',
            'kecamatan' => 'Sangatta Utara',
            'alamat_usaha' => 'Jl. Yos Sudarso II No. 12',
            'nib' => '1234567890123',
            'has_halal' => true,
            'has_pirt' => true,
            'pernah_terima_hibah' => false,
        ]);
        $this->assertNotEmpty($umkm->id);

        // 4. Create Stand
        $stand = EventStand::create([
            'event_id' => $event->id,
            'umkm_id' => $umkm->id,
            'nomor_stand' => 'A-01',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'fasilitas_detail' => ['meja' => 1, 'kursi' => 2, 'air_bersih' => true],
            'pin_pos' => '123456',
            'total_omzet_terekam' => 0.00,
            'total_transaksi_terekam' => 0,
        ]);
        $this->assertEquals($event->id, $stand->event->id);
        $this->assertEquals($umkm->id, $stand->umkmProfile->id);

        // 5. Create StandMenu
        $menu = StandMenu::create([
            'stand_id' => $stand->id,
            'nama_item' => 'Amplang Kuku Macan Super 250g',
            'harga' => 35000.00,
            'is_available' => true,
        ]);
        $this->assertEquals($stand->id, $menu->stand->id);

        // 6. Create Attendee
        $attendee = Attendee::create([
            'event_id' => $event->id,
            'nama_lengkap' => 'Ahmad Fauzi',
            'nomor_wa' => '081298765432',
            'kecamatan' => 'Sangatta Selatan',
            'nomor_tiket_doorprize' => 'PATEN-TEST-' . Str::upper(Str::random(6)),
            'tipe_registrasi' => 'doorprize',
            'is_checked_in' => false,
        ]);
        $this->assertEquals($event->id, $attendee->event->id);

        // Cleanup
        $attendee->delete();
        $menu->delete();
        $stand->delete();
        $umkm->delete();
        $rundown->delete();
        $event->delete();
    }

    /**
     * Test VerifyCmsTokenMiddleware rejects requests without token (401).
     */
    public function test_verify_cms_token_middleware_rejects_missing_token(): void
    {
        $event = Event::create([
            'slug' => 'cms-token-test-' . Str::lower(Str::random(6)),
            'nama_event' => 'CMS Token Test',
            'deskripsi' => 'Test event',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDay(),
            'secure_management_token' => Str::random(64),
        ]);

        $response = $this->get('/cms/' . $event->slug);
        $response->assertStatus(401);

        $event->delete();
    }

    /**
     * Test VerifyCmsTokenMiddleware rejects requests with invalid token (403).
     */
    public function test_verify_cms_token_middleware_rejects_invalid_token(): void
    {
        $event = Event::create([
            'slug' => 'cms-token-test-' . Str::lower(Str::random(6)),
            'nama_event' => 'CMS Token Test',
            'deskripsi' => 'Test event',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDay(),
            'secure_management_token' => Str::random(64),
        ]);

        $response = $this->get('/cms/' . $event->slug . '?token=wrong_invalid_secret_token');
        $response->assertStatus(403);

        $event->delete();
    }

    /**
     * Test VerifyCmsTokenMiddleware allows requests with valid secret token (200).
     */
    public function test_verify_cms_token_middleware_allows_valid_token(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'cms-token-test-' . Str::lower(Str::random(6)),
            'nama_event' => 'CMS Token Test',
            'deskripsi' => 'Test event',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDay(),
            'secure_management_token' => $token,
        ]);

        $response = $this->get('/cms/' . $event->slug . '?token=' . $token);
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'CMS Authorized Access',
            ]);

        // Also test header auth
        $headerResponse = $this->withHeaders(['X-CMS-Token' => $token])
            ->get('/cms/' . $event->slug);
        $headerResponse->assertStatus(200);

        $event->delete();
    }

    /**
     * Test VerifyPosPinMiddleware rejects invalid PIN and allows correct PIN.
     */
    public function test_verify_pos_pin_middleware_enforces_correct_pin(): void
    {
        $event = Event::create([
            'slug' => 'pos-pin-test-' . Str::lower(Str::random(6)),
            'nama_event' => 'POS Pin Test',
            'deskripsi' => 'Test',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDay(),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'PIN-01',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '654321',
        ]);

        // 1. Missing / wrong PIN returns 403
        $respDenied = $this->get('/pos/' . $stand->id . '/test');
        $respDenied->assertStatus(403);

        $respWrong = $this->withHeaders(['X-Stand-Pin' => '000000'])
            ->get('/pos/' . $stand->id . '/test');
        $respWrong->assertStatus(403);

        // 2. Correct PIN in header returns 200
        $respAllowed = $this->withHeaders(['X-Stand-Pin' => '654321'])
            ->get('/pos/' . $stand->id . '/test');
        $respAllowed->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'POS Authorized Access',
            ]);

        // 3. Established session returns 200
        $respSession = $this->withSession(['pos_stand_id' => $stand->id])
            ->get('/pos/' . $stand->id . '/test');
        $respSession->assertStatus(200);

        $stand->delete();
        $event->delete();
    }
}
