<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRundown;
use App\Models\EventStand;
use App\Models\UmkmProfile;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FrontendStage3PublicMicrositeTest extends TestCase
{
    /**
     * Test public event microsite renders Events/Show with event, rundowns, and qrCodeUrl props.
     */
    public function test_public_event_microsite_renders_show_component_with_props(): void
    {
        $event = Event::create([
            'slug' => 'microsite-test-'.Str::lower(Str::random(6)),
            'nama_event' => 'Gebyar Niaga Kutai Timur 2026',
            'deskripsi' => 'Pesta UMKM resmi Kabupaten Kutai Timur',
            'penyelenggara' => 'Dinas Koperasi & UKM Kab. Kutai Timur',
            'lokasi_venue' => 'Kawasan Pusat Pemerintahan Bukit Pelangi, Sangatta',
            'tanggal_mulai' => now()->addDays(2),
            'tanggal_selesai' => now()->addDays(5),
            'secure_management_token' => Str::random(64),
            'gamifikasi_mode' => 'doorprize',
        ]);

        EventRundown::create([
            'event_id' => $event->id,
            'judul_sesi' => 'Pembukaan Gebyar & Bazar Stand UMKM',
            'waktu_mulai' => now()->addDays(2)->setTime(9, 0),
            'waktu_selesai' => now()->addDays(2)->setTime(12, 0),
            'pengisi_acara' => 'Bupati Kutai Timur',
            'urutan' => 1,
        ]);

        $response = $this->get('/e/'.$event->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Events/Show')
            ->has('event')
            ->where('event.nama_event', 'Gebyar Niaga Kutai Timur 2026')
            ->has('rundowns', 1)
            ->has('qrCodeUrl')
        );

        // Cleanup
        $event->rundowns()->delete();
        $event->delete();
    }

    /**
     * Test visitor attendance registration returns unique ticket code and QR code data URL.
     */
    public function test_visitor_attendance_registration_returns_unique_ticket(): void
    {
        $event = Event::create([
            'slug' => 'attendance-test-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Budaya Sangatta',
            'deskripsi' => 'Bazar dan pameran budaya',
            'penyelenggara' => 'Pemkab Kutim',
            'lokasi_venue' => 'Sangatta Utara',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
            'gamifikasi_mode' => 'doorprize',
        ]);

        $payload = [
            'nama_lengkap' => 'Dewi Lestari',
            'nomor_wa' => '081234567890',
            'kecamatan' => 'Sangatta Utara',
            'tipe_registrasi' => 'doorprize',
        ];

        $response = $this->postJson('/e/'.$event->slug.'/register-attendance', $payload);

        $response->assertStatus(201);
        $response->assertJsonStructure([
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
            'nama_lengkap' => 'Dewi Lestari',
        ]);

        // Cleanup
        $event->attendees()->delete();
        $event->delete();
    }

    /**
     * Test public leaderboard renders Events/Leaderboard and strictly conceals financial omzet numbers.
     */
    public function test_public_leaderboard_renders_and_strictly_conceals_financial_data(): void
    {
        $event = Event::create([
            'slug' => 'leaderboard-test-'.Str::lower(Str::random(6)),
            'nama_event' => 'Bazar UMKM Juara Sangatta',
            'deskripsi' => 'Awarding stand teraktif',
            'penyelenggara' => 'Dinas Koperasi & UKM Kab. Kutai Timur',
            'lokasi_venue' => 'Townhall Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => Str::random(64),
            'gamifikasi_mode' => 'doorprize',
        ]);

        $secretNik = '6404'.random_int(100000000000, 999999999999);
        $secretNoKk = '6404'.random_int(100000000000, 999999999999);
        $secretWa = '08'.random_int(1000000000, 9999999999);

        $umkm = UmkmProfile::create([
            'nama_usaha' => 'Amplang Khas Kutim Makmur',
            'nama_pemilik' => 'Ibu Siti Rahma',
            'nik' => $secretNik,
            'no_kk' => $secretNoKk,
            'nomor_wa' => $secretWa,
            'alamat_usaha' => 'Jl. Yos Sudarso No. 12',
            'kecamatan' => 'Sangatta Utara',
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'umkm_id' => $umkm->id,
            'nomor_stand' => 'A-01',
            'kategori_jualan' => 'kuliner',
            'pin_pos' => '123456',
            'total_transaksi_terekam' => 45,
            'total_omzet_terekam' => 12500000,
        ]);

        // Inertia render check
        $response = $this->get('/e/'.$event->slug.'/leaderboard');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Events/Leaderboard')
            ->has('event')
            ->has('leaderboard', 1)
            ->where('leaderboard.0.nomor_stand', 'A-01')
            ->where('leaderboard.0.nama_usaha', 'Amplang Khas Kutim Makmur')
            ->where('leaderboard.0.poin_keaktifan', 450)
        );

        // Strict Financial and PII Data Masking Verification:
        // Response MUST NEVER contain omzet numbers, NIK, No KK, or private phone numbers!
        $content = $response->getContent();
        $this->assertStringNotContainsString('12500000', $content);
        $this->assertStringNotContainsString('total_omzet_terekam', $content);
        $this->assertStringNotContainsString($secretNik, $content);
        $this->assertStringNotContainsString($secretNoKk, $content);
        $this->assertStringNotContainsString($secretWa, $content);

        // Cleanup
        $stand->delete();
        $umkm->delete();
        $event->delete();
    }
}
