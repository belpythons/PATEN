<?php

namespace Tests\Feature;

use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class Stage3CmsManagementAndGateTest extends TestCase
{
    /**
     * Test CMS dashboard requires valid 64-char secret token.
     */
    public function test_cms_dashboard_requires_valid_secret_token(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'cms-dash-test-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Raya Sangatta',
            'deskripsi' => 'Pesta rakyat tahunan',
            'penyelenggara' => 'Pemkab Kutai Timur',
            'lokasi_venue' => 'Kawasan Bukit Pelangi',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => $token,
        ]);

        // 1. Without token -> 401
        $noTokenResp = $this->get('/cms/'.$event->slug);
        $noTokenResp->assertStatus(401);

        // 2. Invalid token -> 403
        $invalidTokenResp = $this->get('/cms/'.$event->slug.'?token=wrong_token_12345');
        $invalidTokenResp->assertStatus(403);

        // 3. Valid token -> 200
        $validTokenResp = $this->get('/cms/'.$event->slug.'?token='.$token);
        $validTokenResp->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'event' => ['id', 'nama_event', 'slug'],
                'metrics' => ['total_stands', 'total_attendees', 'checked_in_attendees', 'total_omzet'],
                'rundowns',
                'stands',
            ]);

        // Cleanup
        $event->delete();
    }

    /**
     * Test CMS rundown CRUD operations (store, update, destroy).
     */
    public function test_cms_rundown_crud_operations(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'rundown-crud-'.Str::lower(Str::random(6)),
            'nama_event' => 'Festival Tari Pedalaman',
            'deskripsi' => 'Festival seni tari Dayak',
            'penyelenggara' => 'Disdikbud Kutim',
            'lokasi_venue' => 'Gedung Expo',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => $token,
        ]);

        // 1. CREATE rundown session
        $createPayload = [
            'judul_sesi' => 'Pembukaan & Tarian Selamat Datang',
            'waktu_mulai' => now()->addHours(2)->toDateTimeString(),
            'waktu_selesai' => now()->addHours(3)->toDateTimeString(),
            'pengisi_acara' => 'Sanggar Seni Telenta Sangatta',
            'urutan' => 1,
        ];

        $createResp = $this->postJson('/cms/'.$event->slug.'/rundowns?token='.$token, $createPayload);
        $createResp->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Sesi rundown berhasil ditambahkan.',
            ]);

        $rundownId = $createResp->json('rundown.id');
        $this->assertNotNull($rundownId);
        $this->assertDatabaseHas('event_rundowns', [
            'id' => $rundownId,
            'event_id' => $event->id,
            'judul_sesi' => 'Pembukaan & Tarian Selamat Datang',
        ]);

        // 2. UPDATE rundown session
        $updatePayload = [
            'judul_sesi' => 'Sambutan Kadisdikbud & Tarian Pembuka',
            'pengisi_acara' => 'Kepala Dinas & Sanggar Seni',
        ];

        $updateResp = $this->putJson('/cms/'.$event->slug.'/rundowns/'.$rundownId.'?token='.$token, $updatePayload);
        $updateResp->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Sesi rundown berhasil diperbarui.',
            ]);

        $this->assertDatabaseHas('event_rundowns', [
            'id' => $rundownId,
            'judul_sesi' => 'Sambutan Kadisdikbud & Tarian Pembuka',
        ]);

        // 3. DELETE rundown session
        $deleteResp = $this->deleteJson('/cms/'.$event->slug.'/rundowns/'.$rundownId.'?token='.$token);
        $deleteResp->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Sesi rundown berhasil dihapus.',
            ]);

        $this->assertDatabaseMissing('event_rundowns', [
            'id' => $rundownId,
        ]);

        // Cleanup
        $event->delete();
    }

    /**
     * Test batch stand provisioning generates unique 6-digit PINs.
     */
    public function test_batch_stand_provisioning_generates_unique_six_digit_pins(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'stand-prov-'.Str::lower(Str::random(6)),
            'nama_event' => 'Kutim Kuliner Expo 2026',
            'deskripsi' => 'Sentra kuliner nusantara',
            'penyelenggara' => 'Diskop UKM',
            'lokasi_venue' => 'Taman Bersemi',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => $token,
        ]);

        $payload = [
            'jumlah_stand' => 10,
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'fasilitas_detail' => [
                'meja' => 2,
                'kursi' => 4,
                'tenda' => 'Sarnafil 3x3',
                'air_bersih' => true,
            ],
        ];

        $response = $this->postJson('/cms/'.$event->slug.'/stands?token='.$token, $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => '10 stand berhasil dibuat beserta PIN kasir.',
            ]);

        $stands = $response->json('stands');
        $this->assertCount(10, $stands);

        $pins = [];
        foreach ($stands as $idx => $stand) {
            $expectedNumber = sprintf('Stand %02d', $idx + 1);
            $this->assertEquals($expectedNumber, $stand['nomor_stand']);
            $this->assertEquals('kuliner', $stand['kategori_jualan']);
            $this->assertEquals(900, $stand['daya_listrik_watt']);

            // Validate 6 digit PIN format
            $this->assertMatchesRegularExpression('/^\d{6}$/', $stand['pin_pos']);
            $pins[] = $stand['pin_pos'];
        }

        // All 10 generated PINs must be unique
        $this->assertCount(10, array_unique($pins));

        // Cleanup
        $event->delete();
    }

    /**
     * Test gate check-in updates attendee status and timestamp.
     */
    public function test_gate_check_in_updates_attendee_status(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'gate-checkin-'.Str::lower(Str::random(6)),
            'nama_event' => 'Konser Musik Kutim Hebat',
            'deskripsi' => 'Konser band lokal',
            'penyelenggara' => 'Kreatif Kutim',
            'lokasi_venue' => 'Stadion Utama Kudungga',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(1),
            'secure_management_token' => $token,
        ]);

        $ticketNumber = 'PATEN-DOOR-'.date('Ymd').'-TEST01';
        $attendee = Attendee::create([
            'event_id' => $event->id,
            'nama_lengkap' => 'Muhammad Fajar',
            'nomor_wa' => '081399887766',
            'kecamatan' => 'Sangatta Selatan',
            'nomor_tiket_doorprize' => $ticketNumber,
            'tipe_registrasi' => 'doorprize',
            'is_checked_in' => false,
        ]);

        $payload = [
            'nomor_tiket_doorprize' => $ticketNumber,
        ];

        $response = $this->postJson('/cms/'.$event->slug.'/check-in?token='.$token, $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'attendee' => [
                    'id' => $attendee->id,
                    'nomor_tiket_doorprize' => $ticketNumber,
                    'nama_lengkap' => 'Muhammad Fajar',
                ],
            ]);

        $attendee->refresh();
        $this->assertTrue($attendee->is_checked_in);
        $this->assertNotNull($attendee->check_in_time);

        // Cleanup
        $event->delete();
    }

    /**
     * Test gate check-in rejects non-existent and already checked-in tickets.
     */
    public function test_gate_check_in_rejects_duplicate_or_invalid_ticket(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'gate-reject-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pameran Otomotif Kutim',
            'deskripsi' => 'Expo modifikasi',
            'penyelenggara' => 'Komunitas Otomotif',
            'lokasi_venue' => 'Stadion Kudungga',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(1),
            'secure_management_token' => $token,
        ]);

        // 1. Non-existent ticket -> 404
        $fakeResponse = $this->postJson('/cms/'.$event->slug.'/check-in?token='.$token, [
            'nomor_tiket_doorprize' => 'PATEN-FAKE-99999999-XXXX',
        ]);
        $fakeResponse->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Tiket tidak ditemukan pada event ini.',
            ]);

        // 2. Already checked-in ticket -> 409
        $ticketNumber = 'PATEN-DOOR-'.date('Ymd').'-TEST02';
        $attendee = Attendee::create([
            'event_id' => $event->id,
            'nama_lengkap' => 'Dewi Anggraini',
            'nomor_wa' => '082144556677',
            'kecamatan' => 'Bengalon',
            'nomor_tiket_doorprize' => $ticketNumber,
            'tipe_registrasi' => 'doorprize',
            'is_checked_in' => true,
            'check_in_time' => now()->subHour(),
        ]);

        $duplicateResponse = $this->postJson('/cms/'.$event->slug.'/check-in?token='.$token, [
            'nomor_tiket_doorprize' => $ticketNumber,
        ]);
        $duplicateResponse->assertStatus(409)
            ->assertJson([
                'success' => false,
            ]);

        // Cleanup
        $event->delete();
    }

    /**
     * Test doorprize drawing only selects attendees who have checked in.
     */
    public function test_doorprize_drawing_only_picks_checked_in_attendees(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'doorprize-draw-'.Str::lower(Str::random(6)),
            'nama_event' => 'Gebyar Pesta Rakyat Kutim',
            'deskripsi' => 'Undian Doorprize Utama Motor & Kulkas',
            'penyelenggara' => 'Panitia HUT Kutim',
            'lokasi_venue' => 'Halaman Kantor Bupati',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => $token,
        ]);

        // Attendee 1: checked in
        $att1 = Attendee::create([
            'event_id' => $event->id,
            'nama_lengkap' => 'Peserta Hadir Satu',
            'nomor_wa' => '081111111111',
            'kecamatan' => 'Sangatta Utara',
            'nomor_tiket_doorprize' => 'PATEN-DOOR-'.date('Ymd').'-WIN01',
            'tipe_registrasi' => 'doorprize',
            'is_checked_in' => true,
            'check_in_time' => now(),
            'is_doorprize_winner' => false,
        ]);

        // Attendee 2: checked in
        $att2 = Attendee::create([
            'event_id' => $event->id,
            'nama_lengkap' => 'Peserta Hadir Dua',
            'nomor_wa' => '082222222222',
            'kecamatan' => 'Teluk Pandan',
            'nomor_tiket_doorprize' => 'PATEN-DOOR-'.date('Ymd').'-WIN02',
            'tipe_registrasi' => 'doorprize',
            'is_checked_in' => true,
            'check_in_time' => now(),
            'is_doorprize_winner' => false,
        ]);

        // Attendee 3: NOT checked in (absent)
        $att3 = Attendee::create([
            'event_id' => $event->id,
            'nama_lengkap' => 'Peserta Tidak Hadir',
            'nomor_wa' => '083333333333',
            'kecamatan' => 'Muara Wahau',
            'nomor_tiket_doorprize' => 'PATEN-DOOR-'.date('Ymd').'-ABS01',
            'tipe_registrasi' => 'doorprize',
            'is_checked_in' => false,
            'is_doorprize_winner' => false,
        ]);

        // Draw 1 winner
        $response = $this->postJson('/cms/'.$event->slug.'/draw-doorprize?token='.$token, [
            'jumlah_pemenang' => 1,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $winners = $response->json('winners');
        $this->assertCount(1, $winners);
        $winnerId = $winners[0]['id'];

        // Winner MUST be either att1 or att2, NEVER att3
        $this->assertContains($winnerId, [$att1->id, $att2->id]);
        $this->assertNotEquals($att3->id, $winnerId);

        // Verify att3 remains non-winner in DB
        $att3->refresh();
        $this->assertFalse($att3->is_doorprize_winner);

        // Cleanup
        $event->delete();
    }
}
