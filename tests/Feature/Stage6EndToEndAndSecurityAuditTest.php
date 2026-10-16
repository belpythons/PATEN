<?php

namespace Tests\Feature;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\EventStand;
use App\Models\PosTransaction;
use App\Models\SpkUmkmEvaluation;
use App\Models\StandMenu;
use App\Models\UmkmProfile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

class Stage6EndToEndAndSecurityAuditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /**
     * Test full end-to-end event lifecycle orchestration from creation to policy evaluation.
     */
    public function test_end_to_end_full_event_lifecycle_simulation(): void
    {
        // 1. Panitia creates event
        $eventPayload = [
            'nama_event' => 'Pekan Akbar Nusantara Kutai Timur 2026',
            'deskripsi' => 'Pesta rakyat dan pameran ekonomi kreatif terpadu',
            'penyelenggara' => 'Pemerintah Kabupaten Kutai Timur',
            'lokasi_venue' => 'Kawasan Bukit Pelangi, Sangatta',
            'tanggal_mulai' => now()->addDay()->toDateTimeString(),
            'tanggal_selesai' => now()->addDays(4)->toDateTimeString(),
            'color_mode' => 'duo',
            'color_primary' => '#0F766E',
            'color_secondary' => '#F59E0B',
            'gamifikasi_mode' => 'doorprize',
            'initial_rundowns' => [
                [
                    'judul_sesi' => 'Seremoni Pembukaan oleh Bupati',
                    'waktu_mulai' => now()->addDay()->addHours(9)->toDateTimeString(),
                    'waktu_selesai' => now()->addDay()->addHours(11)->toDateTimeString(),
                    'pengisi_acara' => 'Bupati Kutai Timur',
                    'urutan' => 1,
                ],
            ],
        ];

        $createEventResp = $this->postJson('/events', $eventPayload);
        $createEventResp->assertStatus(201);

        $eventSlug = $createEventResp->json('data.slug');
        $cmsToken = $createEventResp->json('data.secure_management_token');
        $this->assertNotEmpty($eventSlug);
        $this->assertEquals(64, strlen($cmsToken));

        // 2. Panitia provisions 2 stands via CMS
        $standProvisionResp = $this->postJson("/cms/{$eventSlug}/stands?token={$cmsToken}", [
            'jumlah_stand' => 2,
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'fasilitas_detail' => ['meja' => 2, 'kursi' => 4],
        ]);
        $standProvisionResp->assertStatus(201);
        $stands = $standProvisionResp->json('stands');
        $this->assertCount(2, $stands);

        $stand1Id = $stands[0]['id'];
        $stand1Pin = $stands[0]['pin_pos'];

        // 3. Tenant registers UMKM & initial menu
        $nikTenant = '640804' . str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);
        $kkTenant = '640804' . str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);
        $assignResp = $this->postJson("/stands/{$stand1Id}/assign", [
            'nik' => $nikTenant,
            'no_kk' => $kkTenant,
            'nama_pemilik' => 'Ibu Rahmawati',
            'nama_usaha' => 'Amplang Khas Teluk Pandan',
            'nomor_wa' => '081234567899',
            'kecamatan' => 'Teluk Pandan',
            'nib' => '912030000001',
            'has_halal' => true,
            'has_pirt' => true,
            'initial_menus' => [
                ['nama_item' => 'Amplang Tenggiri Super', 'harga' => 25000],
                ['nama_item' => 'Kerupuk Ikan Gabus', 'harga' => 20000],
            ],
        ]);
        $assignResp->assertStatus(200);

        $menu = StandMenu::where('stand_id', $stand1Id)->first();
        $this->assertNotNull($menu);

        // 4. Visitor registers attendance (doorprize ticket)
        $registerResp = $this->postJson("/e/{$eventSlug}/register-attendance", [
            'nama_lengkap' => 'Ahmad Fauzi',
            'nomor_wa' => '082155667788',
            'kecamatan' => 'Sangatta Utara',
            'tipe_registrasi' => 'doorprize',
        ]);
        $registerResp->assertStatus(201);
        $ticketNumber = $registerResp->json('ticket.nomor_tiket_doorprize');
        $this->assertStringStartsWith('PATEN-DOOR-', $ticketNumber);

        // 5. Gate Check-in
        $checkInResp = $this->postJson("/cms/{$eventSlug}/check-in?token={$cmsToken}", [
            'nomor_tiket_doorprize' => $ticketNumber,
        ]);
        $checkInResp->assertStatus(200);
        $this->assertTrue(Attendee::where('nomor_tiket_doorprize', $ticketNumber)->first()->is_checked_in);

        // 6. Cashier logins with PIN & records transactions
        $loginResp = $this->postJson("/pos/{$stand1Id}/login-pin", [
            'pin_pos' => $stand1Pin,
        ]);
        $loginResp->assertStatus(200);

        // 2 atomic transactions (1 cash, 1 qris)
        $tx1Resp = $this->withSession(['pos_stand_id' => $stand1Id])
            ->postJson("/pos/{$stand1Id}/transactions", [
                'menu_id' => $menu->id,
                'qty' => 2, // 2 * 25000 = 50000
                'metode_pembayaran' => 'cash',
            ]);
        $tx1Resp->assertStatus(201);

        $tx2Resp = $this->withSession(['pos_stand_id' => $stand1Id])
            ->postJson("/pos/{$stand1Id}/transactions", [
                'menu_id' => $menu->id,
                'qty' => 4, // 4 * 25000 = 100000
                'metode_pembayaran' => 'qris',
            ]);
        $tx2Resp->assertStatus(201);

        $standModel = EventStand::find($stand1Id);
        $this->assertEquals(150000.00, (float) $standModel->total_omzet_terekam);
        $this->assertEquals(2, $standModel->total_transaksi_terekam);

        // 7. Panitia draws doorprize winner
        $doorprizeResp = $this->postJson("/cms/{$eventSlug}/draw-doorprize?token={$cmsToken}", [
            'jumlah_pemenang' => 1,
        ]);
        $doorprizeResp->assertStatus(200);
        $winners = $doorprizeResp->json('winners');
        $this->assertCount(1, $winners);
        $this->assertEquals($ticketNumber, $winners[0]['nomor_tiket_doorprize']);

        // 8. Panitia evaluates SPK
        $spkResp = $this->postJson("/cms/{$eventSlug}/evaluate-spk?token={$cmsToken}");
        $spkResp->assertStatus(200);

        // 9. Colab ML Sync
        $mlSyncResp = $this->postJson('/api/ml/sync-evaluations', [
            'event_id' => $standModel->event_id,
            'evaluations' => [
                [
                    'stand_id' => $stand1Id,
                    'cluster_kategori' => 'Established High Value',
                    'rekomendasi_program' => 'hibah_modal_apbd',
                    'skor_akhir_spk' => 95.00,
                ],
            ],
        ]);
        $mlSyncResp->assertStatus(200);

        // 10. Public views leaderboard (zero omzet/identity leaks)
        $leaderboardResp = $this->getJson("/e/{$eventSlug}/leaderboard");
        $leaderboardResp->assertStatus(200)
            ->assertJsonStructure([
                'event',
                'leaderboard' => [
                    '*' => ['peringkat', 'nomor_stand', 'nama_usaha', 'poin_keaktifan'],
                ],
            ]);

        // 11. Panitia views final evaluations report
        $reportResp = $this->getJson("/cms/{$eventSlug}/evaluations?token={$cmsToken}");
        $reportResp->assertStatus(200)
            ->assertJsonStructure([
                'event',
                'evaluations',
                'summary',
            ]);

        // Cleanup
        $event = Event::where('slug', $eventSlug)->first();
        if ($event) {
            $event->delete(); // Cascades stands, attendees, rundowns, evaluations
        }
        UmkmProfile::where('nik', $nikTenant)->delete();
    }

    /**
     * Test atomic concurrency integrity of POS transactions (no lost updates).
     */
    public function test_concurrent_pos_transactions_atomic_integrity(): void
    {
        $event = Event::create([
            'slug' => 'conc-pos-' . Str::lower(Str::random(6)),
            'nama_event' => 'Concurrency Benchmark Event',
            'deskripsi' => 'Benchmark',
            'penyelenggara' => 'Diskop UKM',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand C-01',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'pin_pos' => '999888',
            'total_omzet_terekam' => 0.00,
            'total_transaksi_terekam' => 0,
        ]);

        $menu = StandMenu::create([
            'stand_id' => $stand->id,
            'nama_item' => 'Kopi Panas Sangatta',
            'harga' => 12500.00,
            'is_available' => true,
        ]);

        $numTransactions = 25;
        $expectedTotalOmzet = 0.0;

        for ($i = 1; $i <= $numTransactions; $i++) {
            $qty = ($i % 3) + 1; // 1, 2, or 3
            $subtotal = 12500.00 * $qty;
            $expectedTotalOmzet += $subtotal;

            $resp = $this->withSession(['pos_stand_id' => $stand->id])
                ->postJson("/pos/{$stand->id}/transactions", [
                    'menu_id' => $menu->id,
                    'qty' => $qty,
                    'metode_pembayaran' => ($i % 2 === 0) ? 'qris' : 'cash',
                ]);

            $resp->assertStatus(201);
        }

        $stand->refresh();

        // Exact match with zero tolerance for rupiah loss
        $this->assertEquals($numTransactions, $stand->total_transaksi_terekam);
        $this->assertEquals($expectedTotalOmzet, (float) $stand->total_omzet_terekam);
        $this->assertEquals($numTransactions, PosTransaction::where('stand_id', $stand->id)->count());

        // Cleanup
        $stand->posTransactions()->delete();
        $stand->menus()->delete();
        $stand->delete();
        $event->delete();
    }

    /**
     * Test comprehensive security audit ensuring zero sensitive data leaks on public endpoints.
     */
    public function test_comprehensive_security_audit_zero_data_leaks(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'sec-audit-' . Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Wisata Danau Redan',
            'deskripsi' => 'Pameran Pariwisata dan Kriya',
            'penyelenggara' => 'Dinas Pariwisata Kutim',
            'lokasi_venue' => 'Kecamatan Teluk Pandan',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => $token,
        ]);

        $sensitiveNik = '640804' . str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);
        $sensitiveKk = '640804' . str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);
        $sensitiveWa = '0812' . str_pad((string) random_int(1, 99999999), 8, '0', STR_PAD_LEFT);

        try {
            $umkm = UmkmProfile::create([
                'nik' => $sensitiveNik,
                'no_kk' => $sensitiveKk,
                'nama_pemilik' => 'Pak Sulaiman',
                'nama_usaha' => 'Madu Hutan Asli Kutim',
                'nomor_wa' => $sensitiveWa,
                'kecamatan' => 'Teluk Pandan',
                'nib' => '1928374650',
            ]);

            $stand = EventStand::create([
                'event_id' => $event->id,
                'umkm_id' => $umkm->id,
                'nomor_stand' => 'Stand P-01',
                'kategori_jualan' => 'agribisnis',
                'daya_listrik_watt' => 450,
                'pin_pos' => '543210',
                'total_omzet_terekam' => 24500000.00,
                'total_transaksi_terekam' => 140,
            ]);

            SpkUmkmEvaluation::create([
                'event_id' => $event->id,
                'stand_id' => $stand->id,
                'umkm_id' => $umkm->id,
                'score_konsistensi_transaksi' => 0.90,
                'score_digitalisasi_qris' => 0.75,
                'score_kepatuhan_regulasi' => 1.0,
                'skor_akhir_spk' => 91.00,
                'cluster_kategori' => 'Established High Value',
                'rekomendasi_program' => 'hibah_modal_apbd',
                'is_prioritas_hibah_200' => true,
            ]);

            // Audit Endpoint 1: Public Event Landing (/e/{slug})
            $respLanding = $this->getJson("/e/{$event->slug}");
            $respLanding->assertStatus(200);
            $contentLanding = $respLanding->getContent();

            $this->assertStringNotContainsString($sensitiveNik, $contentLanding);
            $this->assertStringNotContainsString($sensitiveKk, $contentLanding);
            $this->assertStringNotContainsString($sensitiveWa, $contentLanding);
            $this->assertStringNotContainsString('24500000', $contentLanding);
            $this->assertStringNotContainsString($token, $contentLanding);

            // Audit Endpoint 2: Public Leaderboard (/e/{slug}/leaderboard)
            $respLeaderboard = $this->getJson("/e/{$event->slug}/leaderboard");
            $respLeaderboard->assertStatus(200);
            $contentLeaderboard = $respLeaderboard->getContent();

            $this->assertStringNotContainsString($sensitiveNik, $contentLeaderboard);
            $this->assertStringNotContainsString($sensitiveKk, $contentLeaderboard);
            $this->assertStringNotContainsString($sensitiveWa, $contentLeaderboard);
            $this->assertStringNotContainsString('24500000', $contentLeaderboard);
            $this->assertStringNotContainsString('total_omzet_terekam', $contentLeaderboard);
            $this->assertStringNotContainsString($token, $contentLeaderboard);

            // Audit Endpoint 3: Poster Export Metadata (/events/{slug}/poster-export)
            $respPoster = $this->getJson("/events/{$event->slug}/poster-export");
            $respPoster->assertStatus(200);
            $contentPoster = $respPoster->getContent();

            $this->assertStringNotContainsString($sensitiveNik, $contentPoster);
            $this->assertStringNotContainsString($sensitiveKk, $contentPoster);
            $this->assertStringNotContainsString($sensitiveWa, $contentPoster);
            $this->assertStringNotContainsString($token, $contentPoster);
        } finally {
            // Cleanup
            $event->spkEvaluations()->delete();
            EventStand::where('event_id', $event->id)->delete();
            UmkmProfile::where('nik', $sensitiveNik)->delete();
            $event->delete();
        }
    }

    /**
     * Test brute force prevention on POS cashier PIN (throttle 10 attempts/minute).
     */
    public function test_brute_force_pos_pin_rate_limiting(): void
    {
        $event = Event::create([
            'slug' => 'brute-pin-' . Str::lower(Str::random(6)),
            'nama_event' => 'Brute Force Test Event',
            'deskripsi' => 'Test',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand BF-01',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '777888',
        ]);

        // Perform 10 failed login attempts
        for ($i = 0; $i < 10; $i++) {
            $res = $this->postJson("/pos/{$stand->id}/login-pin", [
                'pin_pos' => '000000',
            ]);
            $this->assertEquals(403, $res->status());
        }

        // 11th attempt must be rejected with 429 Too Many Requests
        $overlimitResp = $this->postJson("/pos/{$stand->id}/login-pin", [
            'pin_pos' => '000000',
        ]);
        $this->assertEquals(429, $overlimitResp->status());

        // Cleanup
        $stand->delete();
        $event->delete();
    }

    /**
     * Test CMS token tampering immunity (missing, empty, and falsified tokens rejected).
     */
    public function test_cms_token_tampering_immunity(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'tamper-cms-' . Str::lower(Str::random(6)),
            'nama_event' => 'Token Tampering Immunity Event',
            'deskripsi' => 'Test',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => $token,
        ]);

        // 1. Missing token -> 401
        $missingResp = $this->getJson("/cms/{$event->slug}");
        $missingResp->assertStatus(401);

        // 2. Falsified / tampered token -> 403
        $tamperedResp = $this->getJson("/cms/{$event->slug}?token=" . Str::random(64));
        $tamperedResp->assertStatus(403);

        // 3. One character off -> 403
        $oneCharOff = substr($token, 0, 63) . ($token[63] === 'a' ? 'b' : 'a');
        $offResp = $this->getJson("/cms/{$event->slug}?token={$oneCharOff}");
        $offResp->assertStatus(403);

        // 4. Correct token -> 200
        $validResp = $this->getJson("/cms/{$event->slug}?token={$token}");
        $validResp->assertStatus(200);

        // Cleanup
        $event->delete();
    }
}
