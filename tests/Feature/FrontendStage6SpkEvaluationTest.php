<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventStand;
use App\Models\UmkmProfile;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FrontendStage6SpkEvaluationTest extends TestCase
{
    /**
     * Test SPK evaluations page requires valid CMS token.
     */
    public function test_spk_evaluations_page_requires_valid_cms_token(): void
    {
        $event = Event::create([
            'slug' => 'spk-auth-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Raya Kutai Timur 2026',
            'deskripsi' => 'Bazar UMKM Resmi',
            'penyelenggara' => 'Dinas Koperasi & UKM Kab. Kutai Timur',
            'lokasi_venue' => 'Kawasan Bukit Pelangi Sangatta',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => Str::random(64),
        ]);

        // Request without token -> 401/403
        $respNoToken = $this->get('/cms/'.$event->slug.'/evaluations');
        $this->assertContains($respNoToken->status(), [401, 403, 302]);

        // Request with invalid token -> 401/403
        $respInvalid = $this->get('/cms/'.$event->slug.'/evaluations?token=wrong_token_xyz');
        $this->assertContains($respInvalid->status(), [401, 403, 302]);

        // Cleanup
        $event->delete();
    }

    /**
     * Test SPK evaluations page renders Inertia Cms/Evaluations with all required props.
     */
    public function test_spk_evaluations_page_renders_inertia_component_with_expected_props(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'spk-render-'.Str::lower(Str::random(6)),
            'nama_event' => 'Bazar Pembangunan Sangatta 2026',
            'deskripsi' => 'Pameran Produk Unggulan Daerah',
            'penyelenggara' => 'Diskop-UKM Kutim',
            'lokasi_venue' => 'Taman Bersemi Sangatta',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => $token,
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 01',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'pin_pos' => '123456',
        ]);

        $response = $this->withHeaders(['X-Test-Inertia' => 'true'])
            ->get('/cms/'.$event->slug.'/evaluations?token='.$token);

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Cms/Evaluations')
            ->has('event')
            ->where('event.nama_event', 'Bazar Pembangunan Sangatta 2026')
            ->has('token')
            ->where('token', $token)
            ->has('evaluations')
            ->has('summary')
            ->has('cms_url')
        );

        // Cleanup
        $stand->delete();
        $event->delete();
    }

    /**
     * Test triggering SPK evaluation executes SAW engine, evaluates Perbup guardrails, and updates database.
     */
    public function test_spk_evaluation_execution_and_guardrail_enforcement(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'spk-exec-'.Str::lower(Str::random(6)),
            'nama_event' => 'Festival Expo Kutim Hebat',
            'deskripsi' => 'Festival',
            'penyelenggara' => 'Diskop-UKM Kutim',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => $token,
        ]);

        // Stand A: Full legal, high volume -> Should qualify for hibah_modal_apbd
        $randomA = str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);
        $umkmA = UmkmProfile::create([
            'nik' => '640804'.$randomA,
            'no_kk' => '640804'.$randomA,
            'nama_pemilik' => 'Hj. Aminah',
            'nama_usaha' => 'Dapur Kuliner Sangatta Prima',
            'nomor_wa' => '081234567891',
            'kecamatan' => 'Sangatta Utara',
            'nib' => '1234567890123',
            'has_halal' => true,
            'has_pirt' => true,
            'pernah_terima_hibah' => false,
        ]);

        $standA = EventStand::create([
            'event_id' => $event->id,
            'umkm_id' => $umkmA->id,
            'nomor_stand' => 'Stand 01',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'pin_pos' => '112233',
            'total_omzet_terekam' => 5000000.00,
            'total_transaksi_terekam' => 150,
        ]);

        // Stand B: Same KK as Stand A with lower performance -> Guardrail rule: 1 KK 1 Penerima APBD
        $umkmB = UmkmProfile::create([
            'nik' => '640804'.str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'no_kk' => '640804'.$randomA, // Duplicate KK!
            'nama_pemilik' => 'Ahmad (Anak Hj. Aminah)',
            'nama_usaha' => 'Minuman Dingin Berkah',
            'nomor_wa' => '081234567892',
            'kecamatan' => 'Sangatta Utara',
            'nib' => '1234567890124',
            'has_halal' => true,
            'has_pirt' => true,
            'pernah_terima_hibah' => false,
        ]);

        $standB = EventStand::create([
            'event_id' => $event->id,
            'umkm_id' => $umkmB->id,
            'nomor_stand' => 'Stand 02',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '445566',
            'total_omzet_terekam' => 4000000.00,
            'total_transaksi_terekam' => 120,
        ]);

        // Stand C: Prior grant recipient -> Anti-duplikasi APBD guardrail
        $randomC = str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);
        $umkmC = UmkmProfile::create([
            'nik' => '640804'.$randomC,
            'no_kk' => '640804'.$randomC,
            'nama_pemilik' => 'Pak Joko',
            'nama_usaha' => 'Kripik Pisang Sangatta',
            'nomor_wa' => '081234567893',
            'kecamatan' => 'Bengalon',
            'nib' => '1234567890125',
            'has_halal' => true,
            'has_pirt' => false,
            'pernah_terima_hibah' => true,
            'tahun_terima_hibah' => 2024,
        ]);

        $standC = EventStand::create([
            'event_id' => $event->id,
            'umkm_id' => $umkmC->id,
            'nomor_stand' => 'Stand 03',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '778899',
            'total_omzet_terekam' => 3000000.00,
            'total_transaksi_terekam' => 80,
        ]);

        // Trigger SPK evaluation via POST endpoint
        $response = $this->withHeaders(['X-Inertia' => 'true'])
            ->post('/cms/'.$event->slug.'/evaluate-spk?token='.$token);

        $response->assertStatus(302);
        $response->assertRedirect('/cms/'.$event->slug.'/evaluations?token='.$token);

        // Verify database evaluations
        $evalA = $standA->spkEvaluation;
        $this->assertNotNull($evalA);
        $this->assertEquals('hibah_modal_apbd', $evalA->rekomendasi_program);
        $this->assertTrue((bool) $evalA->is_prioritas_hibah_200);

        $evalB = $standB->spkEvaluation;
        $this->assertNotNull($evalB);
        // Stand B has duplicate KK with Stand A -> disqualified from APBD and redirected to kredit_perbankan
        $this->assertEquals('kredit_perbankan', $evalB->rekomendasi_program);
        $this->assertFalse((bool) $evalB->is_prioritas_hibah_200);
        $this->assertStringContainsString('kesamaan No KK', $evalB->catatan_dinas);

        $evalC = $standC->spkEvaluation;
        $this->assertNotNull($evalC);
        // Stand C previously received grant -> redirected to kredit_perbankan
        $this->assertEquals('kredit_perbankan', $evalC->rekomendasi_program);
        $this->assertFalse((bool) $evalC->is_prioritas_hibah_200);
        $this->assertStringContainsString('Pernah menerima hibah', $evalC->catatan_dinas);

        // Now verify that visiting the evaluations page returns the populated evaluations
        $this->flushHeaders();
        $evalPageResp = $this->withHeaders(['X-Test-Inertia' => 'true'])
            ->get('/cms/'.$event->slug.'/evaluations?token='.$token);

        $evalPageResp->assertStatus(200);
        $evalPageResp->assertInertia(fn (Assert $page) => $page
            ->component('Cms/Evaluations')
            ->has('evaluations', 3)
            ->where('summary.total_evaluated', 3)
            ->where('summary.prioritas_hibah_200', 1)
            ->where('summary.kredit_perbankan', 2)
        );

        // Cleanup
        $event->spkEvaluations()->delete();
        $standA->delete();
        $standB->delete();
        $standC->delete();
        $umkmA->delete();
        $umkmB->delete();
        $umkmC->delete();
        $event->delete();
    }
}
