<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FrontendStage2EventCreationTest extends TestCase
{
    /**
     * Test create event form renders with Inertia Events/Create and expected default props.
     */
    public function test_create_event_page_renders_with_inertia_and_props(): void
    {
        $response = $this->get('/events/create');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Events/Create')
            ->has('defaultPrimaryColor')
            ->has('defaultSecondaryColor')
            ->has('gamifikasiOptions')
        );
    }

    /**
     * Test event creation via Inertia form submission redirects to CMS secret URL.
     */
    public function test_event_creation_redirects_to_cms_url(): void
    {
        $payload = [
            'nama_event' => 'Pekan Raya Sangatta '.Str::random(4),
            'deskripsi' => 'Festival niaga dan UMKM Kutim',
            'penyelenggara' => 'Dinas Koperasi & UKM Kabupaten Kutai Timur',
            'lokasi_venue' => 'Kawasan Bukit Pelangi, Sangatta',
            'tanggal_mulai' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'tanggal_selesai' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'color_mode' => 'duo',
            'color_primary' => '#132952',
            'color_secondary' => '#00B262',
            'gamifikasi_mode' => 'doorprize',
            'is_awarding_active' => true,
            'initial_rundowns' => [
                [
                    'judul_sesi' => 'Pembukaan Stan & Pameran UMKM',
                    'waktu_mulai' => now()->addDays(1)->setTime(9, 0)->format('Y-m-d H:i:s'),
                    'waktu_selesai' => now()->addDays(1)->setTime(12, 0)->format('Y-m-d H:i:s'),
                    'pengisi_acara' => 'Bupati Kutai Timur',
                    'deskripsi_sesi' => 'Seremonial pembukaan resmi',
                ],
            ],
        ];

        // Inertia form submission sends X-Inertia: true header
        $response = $this->withHeaders(['X-Inertia' => 'true'])->post('/events', $payload);

        $response->assertStatus(302);
        $this->assertDatabaseHas('events', [
            'nama_event' => $payload['nama_event'],
            'color_primary' => '#132952',
            'color_secondary' => '#00B262',
        ]);

        $createdEvent = Event::where('nama_event', $payload['nama_event'])->first();
        $this->assertNotNull($createdEvent);
        $this->assertStringContainsString('/cms/'.$createdEvent->slug, $response->headers->get('Location'));
        $this->assertStringContainsString('token='.$createdEvent->secure_management_token, $response->headers->get('Location'));

        // Cleanup
        $createdEvent->delete();
    }

    /**
     * Test poster export endpoint renders Inertia Events/PosterExport when accessed as web page.
     */
    public function test_poster_export_renders_inertia_component_for_web(): void
    {
        $event = Event::create([
            'slug' => 'poster-web-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Budaya Sangatta',
            'deskripsi' => 'Pameran seni daerah',
            'penyelenggara' => 'Disporapar Kutim',
            'lokasi_venue' => 'Taman Bersemi',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => Str::random(64),
            'color_mode' => 'duo',
            'color_primary' => '#132952',
            'color_secondary' => '#00B262',
            'gamifikasi_mode' => 'doorprize',
        ]);

        $response = $this->get('/events/'.$event->slug.'/poster-export');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Events/PosterExport')
            ->has('poster')
            ->where('poster.nama_event', 'Pekan Budaya Sangatta')
            ->where('poster.color_primary', '#132952')
            ->has('event')
        );

        // Cleanup
        $event->delete();
    }
}
