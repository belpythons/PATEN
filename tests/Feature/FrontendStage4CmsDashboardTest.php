<?php

namespace Tests\Feature;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\EventRundown;
use App\Models\EventStand;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FrontendStage4CmsDashboardTest extends TestCase
{
    /**
     * Test CMS dashboard requires valid 64-character token in query string or header.
     */
    public function test_cms_dashboard_requires_valid_token(): void
    {
        $event = Event::create([
            'slug' => 'cms-auth-test-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Niaga Sangatta 2026',
            'deskripsi' => 'Bazar resmi UMKM',
            'penyelenggara' => 'Dinas Koperasi & UKM Kab. Kutai Timur',
            'lokasi_venue' => 'Kawasan Bukit Pelangi',
            'tanggal_mulai' => now()->addDays(2),
            'tanggal_selesai' => now()->addDays(4),
            'secure_management_token' => Str::random(64),
        ]);

        // Request without token should be blocked by VerifyCmsTokenMiddleware
        $responseNoToken = $this->get('/cms/'.$event->slug);
        $this->assertContains($responseNoToken->status(), [401, 403, 302]);

        // Request with invalid token should be blocked
        $responseInvalidToken = $this->get('/cms/'.$event->slug.'?token=invalid_token_123');
        $this->assertContains($responseInvalidToken->status(), [401, 403, 302]);

        // Cleanup
        $event->delete();
    }

    /**
     * Test CMS dashboard renders Inertia Cms/Dashboard with all required props.
     */
    public function test_cms_dashboard_renders_inertia_component_with_expected_props(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'cms-dashboard-test-'.Str::lower(Str::random(6)),
            'nama_event' => 'Bazar Semarak Sangatta',
            'deskripsi' => 'Pameran ekonomi kreatif',
            'penyelenggara' => 'Diskop-UKM Kutim',
            'lokasi_venue' => 'Taman Bersemi Sangatta',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => $token,
        ]);

        EventRundown::create([
            'event_id' => $event->id,
            'judul_sesi' => 'Seremonial Pembukaan Stand',
            'waktu_mulai' => now()->addDays(1)->setTime(10, 0),
            'waktu_selesai' => now()->addDays(1)->setTime(12, 0),
            'pengisi_acara' => 'Bupati Kutim',
            'urutan' => 1,
        ]);

        EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 01',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '123456',
        ]);

        $response = $this->withHeaders(['X-Test-Inertia' => 'true'])
            ->get('/cms/'.$event->slug.'?token='.$token);

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Cms/Dashboard')
            ->has('event')
            ->where('event.nama_event', 'Bazar Semarak Sangatta')
            ->has('token')
            ->has('metrics')
            ->where('metrics.total_stands', 1)
            ->has('rundowns', 1)
            ->has('stands', 1)
            ->has('recent_attendees')
            ->has('doorprize_winners')
            ->has('public_url')
            ->has('cms_url')
        );

        // Cleanup
        $event->rundowns()->delete();
        $event->stands()->delete();
        $event->delete();
    }

    /**
     * Test adding and deleting rundown sessions via CMS endpoints.
     */
    public function test_cms_rundown_crud_operations(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'cms-rundown-test-'.Str::lower(Str::random(6)),
            'nama_event' => 'Festival Musik & Bazar',
            'deskripsi' => 'Kegiatan kepemudaan',
            'penyelenggara' => 'Disporapar Kutim',
            'lokasi_venue' => 'Bukit Pelangi',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => $token,
        ]);

        $payload = [
            'judul_sesi' => 'Live Music & Akustik',
            'waktu_mulai' => now()->addDays(1)->setTime(19, 0)->format('Y-m-d H:i:s'),
            'waktu_selesai' => now()->addDays(1)->setTime(21, 0)->format('Y-m-d H:i:s'),
            'pengisi_acara' => 'Band Lokal Kutim',
            'urutan' => 1,
        ];

        // Create rundown
        $response = $this->withHeaders(['X-Inertia' => 'true'])
            ->post('/cms/'.$event->slug.'/rundowns?token='.$token, $payload);

        $response->assertStatus(302);
        $this->assertDatabaseHas('event_rundowns', [
            'event_id' => $event->id,
            'judul_sesi' => 'Live Music & Akustik',
        ]);

        $rundown = EventRundown::where('event_id', $event->id)->first();
        $this->assertNotNull($rundown);

        // Delete rundown
        $deleteResponse = $this->withHeaders(['X-Inertia' => 'true'])
            ->delete('/cms/'.$event->slug.'/rundowns/'.$rundown->id.'?token='.$token);

        $this->assertContains($deleteResponse->status(), [302, 303]);
        $this->assertDatabaseMissing('event_rundowns', [
            'id' => $rundown->id,
        ]);

        // Cleanup
        $event->delete();
    }

    /**
     * Test batch stand provisioning generates stands and unique 6-digit PINs.
     */
    public function test_cms_batch_stand_provisioning(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'cms-provision-test-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pameran Pembangunan Kutim',
            'deskripsi' => 'Pesta rakyat tahunan',
            'penyelenggara' => 'Setda Kutai Timur',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => $token,
        ]);

        $payload = [
            'jumlah_stand' => 3,
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'fasilitas_detail' => [
                'meja' => 2,
                'kursi' => 4,
            ],
        ];

        $response = $this->withHeaders(['X-Inertia' => 'true'])
            ->post('/cms/'.$event->slug.'/stands?token='.$token, $payload);

        $response->assertStatus(302);

        $createdStands = EventStand::where('event_id', $event->id)->get();
        $this->assertCount(3, $createdStands);

        foreach ($createdStands as $stand) {
            $this->assertEquals('kuliner', $stand->kategori_jualan);
            $this->assertEquals(900, $stand->daya_listrik_watt);
            $this->assertMatchesRegularExpression('/^\d{6}$/', $stand->pin_pos);
        }

        // Cleanup
        $event->stands()->delete();
        $event->delete();
    }

    /**
     * Test gate check-in and kinetic doorprize draw operations.
     */
    public function test_cms_gate_check_in_and_doorprize_draw(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'cms-gate-test-'.Str::lower(Str::random(6)),
            'nama_event' => 'Bazar Kuliner Nusantara',
            'deskripsi' => 'Festival kuliner daerah',
            'penyelenggara' => 'Dispar Kutim',
            'lokasi_venue' => 'Kutim',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => $token,
        ]);

        $attendee = Attendee::create([
            'event_id' => $event->id,
            'nomor_tiket_doorprize' => 'PATEN-DOOR-'.strtoupper(Str::random(8)),
            'nama_lengkap' => 'Budi Santoso',
            'nomor_wa' => '081234567890',
            'kecamatan' => 'Sangatta Utara',
            'tipe_registrasi' => 'doorprize',
            'is_checked_in' => false,
            'is_doorprize_winner' => false,
        ]);

        // Check-in via gate endpoint
        $checkInResponse = $this->postJson('/cms/'.$event->slug.'/check-in?token='.$token, [
            'nomor_tiket_doorprize' => $attendee->nomor_tiket_doorprize,
        ]);

        $checkInResponse->assertStatus(200);
        $checkInResponse->assertJson([
            'success' => true,
        ]);

        $attendee->refresh();
        $this->assertTrue($attendee->is_checked_in);

        // Draw doorprize for checked-in attendee
        $drawResponse = $this->postJson('/cms/'.$event->slug.'/draw-doorprize?token='.$token, [
            'jumlah_pemenang' => 1,
        ]);

        $drawResponse->assertStatus(200);
        $drawResponse->assertJson([
            'success' => true,
        ]);

        $attendee->refresh();
        $this->assertTrue($attendee->is_doorprize_winner);

        // Cleanup
        $attendee->delete();
        $event->delete();
    }
}
