<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventStand;
use App\Models\PosTransaction;
use App\Models\SpkUmkmEvaluation;
use App\Models\StandMenu;
use App\Models\UmkmProfile;
use App\Services\SpkEngineService;
use Illuminate\Support\Str;
use Tests\TestCase;

class Stage5SpkEngineAndLeaderboardTest extends TestCase
{
    /**
     * Test regulatory guardrail prevents duplicate No KK from receiving APBD grant (1 KK 1 Penerima).
     */
    public function test_regulatory_guardrail_prevents_duplicate_kk_receiving_apbd_grant(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'spk-kk-'.Str::lower(Str::random(6)),
            'nama_event' => 'Expo Kuliner Kutim Hebat',
            'deskripsi' => 'Pameran UMKM',
            'penyelenggara' => 'Dinas Koperasi dan UKM',
            'lokasi_venue' => 'Kawasan Bukit Pelangi',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => $token,
        ]);

        $sameNoKk = '640804'.str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);

        // UMKM 1: Suami (Skor Lebih Tinggi)
        $umkm1 = UmkmProfile::create([
            'nik' => '640804'.str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'no_kk' => $sameNoKk,
            'nama_pemilik' => 'Bambang Irawan',
            'nama_usaha' => 'Sate Payau Sangatta',
            'nomor_wa' => '081211112222',
            'kecamatan' => 'Sangatta Utara',
            'nib' => '9120000001',
            'has_halal' => true,
            'has_pirt' => true,
            'pernah_terima_hibah' => false,
        ]);

        $stand1 = EventStand::create([
            'event_id' => $event->id,
            'umkm_id' => $umkm1->id,
            'nomor_stand' => 'Stand 01',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'pin_pos' => '111111',
            'total_omzet_terekam' => 5000000.00,
            'total_transaksi_terekam' => 50,
        ]);

        // UMKM 2: Istri (1 KK yang sama, performa lebih rendah)
        $umkm2 = UmkmProfile::create([
            'nik' => '640804'.str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'no_kk' => $sameNoKk,
            'nama_pemilik' => 'Siti Aminah',
            'nama_usaha' => 'Minuman Herbal Sangatta',
            'nomor_wa' => '081233334444',
            'kecamatan' => 'Sangatta Utara',
            'nib' => '9120000002',
            'has_halal' => true,
            'has_pirt' => true,
            'pernah_terima_hibah' => false,
        ]);

        $stand2 = EventStand::create([
            'event_id' => $event->id,
            'umkm_id' => $umkm2->id,
            'nomor_stand' => 'Stand 02',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '222222',
            'total_omzet_terekam' => 3000000.00,
            'total_transaksi_terekam' => 30,
        ]);

        // Run evaluation trigger
        $response = $this->postJson('/cms/'.$event->slug.'/evaluate-spk?token='.$token);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total_evaluated' => 2,
            ]);

        $eval1 = SpkUmkmEvaluation::where('stand_id', $stand1->id)->first();
        $eval2 = SpkUmkmEvaluation::where('stand_id', $stand2->id)->first();

        $this->assertNotNull($eval1);
        $this->assertNotNull($eval2);

        // UMKM 1 has higher score and gets APBD grant
        $this->assertGreaterThan($eval2->skor_akhir_spk, $eval1->skor_akhir_spk);
        $this->assertEquals('hibah_modal_apbd', $eval1->rekomendasi_program);
        $this->assertTrue($eval1->is_prioritas_hibah_200);

        // UMKM 2 with same No KK is disqualified from hibah_modal_apbd and redirected
        $this->assertNotEquals('hibah_modal_apbd', $eval2->rekomendasi_program);
        $this->assertFalse($eval2->is_prioritas_hibah_200);
        $this->assertStringContainsString('kesamaan No KK', $eval2->catatan_dinas);

        // Cleanup
        $event->spkEvaluations()->delete();
        $stand1->delete();
        $stand2->delete();
        $umkm1->delete();
        $umkm2->delete();
        $event->delete();
    }

    /**
     * Test regulatory guardrail redirects unverified/missing NIB to fasilitasi_legalitas.
     */
    public function test_regulatory_guardrail_redirects_unverified_nib_to_legalitas_program(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'spk-nib-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pameran Usaha Mikro Sangatta',
            'deskripsi' => 'Test',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => $token,
        ]);

        // UMKM with NO NIB
        $umkm = UmkmProfile::create([
            'nik' => '640804'.str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'no_kk' => '640804'.str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'nama_pemilik' => 'Hendro Santoso',
            'nama_usaha' => 'Gorengan Renyah Sangatta',
            'nomor_wa' => '081299990000',
            'kecamatan' => 'Sangatta Selatan',
            'nib' => null, // No NIB
            'has_halal' => false,
            'has_pirt' => false,
            'pernah_terima_hibah' => false,
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'umkm_id' => $umkm->id,
            'nomor_stand' => 'Stand 03',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '333333',
            'total_omzet_terekam' => 8000000.00,
            'total_transaksi_terekam' => 120, // High transactions, but no NIB
        ]);

        $response = $this->postJson('/cms/'.$event->slug.'/evaluate-spk?token='.$token);
        $response->assertStatus(200);

        $eval = SpkUmkmEvaluation::where('stand_id', $stand->id)->first();
        $this->assertNotNull($eval);
        $this->assertEquals('fasilitasi_legalitas', $eval->rekomendasi_program);
        $this->assertFalse($eval->is_prioritas_hibah_200);
        $this->assertStringContainsString('NIB', $eval->catatan_dinas);

        // Cleanup
        $event->spkEvaluations()->delete();
        $stand->delete();
        $umkm->delete();
        $event->delete();
    }

    /**
     * Test SAW scoring mathematical accuracy according to specified weight matrix.
     */
    public function test_saw_scoring_mathematical_accuracy(): void
    {
        $event = Event::create([
            'slug' => 'saw-math-'.Str::lower(Str::random(6)),
            'nama_event' => 'SAW Math Verification',
            'deskripsi' => 'Test',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
        ]);

        $umkm = UmkmProfile::create([
            'nik' => '640804'.str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'no_kk' => '640804'.str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'nama_pemilik' => 'Juara Bertahan',
            'nama_usaha' => 'Kopi Kutim Mandiri',
            'nomor_wa' => '081288887777',
            'kecamatan' => 'Sangatta Utara',
            'nib' => '1234567890',
            'has_halal' => true,
            'has_pirt' => true,
            'pernah_terima_hibah' => false,
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'umkm_id' => $umkm->id,
            'nomor_stand' => 'Stand 04',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'pin_pos' => '444444',
            'total_omzet_terekam' => 1000000.00,
            'total_transaksi_terekam' => 10,
        ]);

        $menu = StandMenu::create([
            'stand_id' => $stand->id,
            'nama_item' => 'Kopi Tubruk',
            'harga' => 100000.00,
            'is_available' => true,
        ]);

        // Create 8 transactions across 8 distinct hours, 100% QRIS
        for ($h = 1; $h <= 8; $h++) {
            PosTransaction::create([
                'event_id' => $event->id,
                'stand_id' => $stand->id,
                'menu_id' => $menu->id,
                'qty' => 1,
                'subtotal' => 100000.00,
                'metode_pembayaran' => 'qris',
                'created_at' => now()->subHours($h),
            ]);
        }

        $spkService = new SpkEngineService;
        $evaluations = $spkService->evaluate($event);

        $this->assertCount(1, $evaluations);
        $eval = $evaluations->first();

        // All normalized indicators should be 1.0 (100%):
        // 0.30(1.0) + 0.20(1.0) + 0.25(1.0) + 0.10(1.0) + 0.15(1.0) = 1.0 -> 100.00
        $this->assertEquals(100.00, (float) $eval->skor_akhir_spk);
        $this->assertEquals('hibah_modal_apbd', $eval->rekomendasi_program);
        $this->assertTrue($eval->is_prioritas_hibah_200);

        // Cleanup
        $event->spkEvaluations()->delete();
        $stand->posTransactions()->delete();
        $stand->menus()->delete();
        $stand->delete();
        $umkm->delete();
        $event->delete();
    }

    /**
     * Test ML sync webhook updates evaluations table records.
     */
    public function test_ml_sync_webhook_updates_evaluations_table(): void
    {
        $event = Event::create([
            'slug' => 'ml-sync-'.Str::lower(Str::random(6)),
            'nama_event' => 'Paten ML Sync Event',
            'deskripsi' => 'Test',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
        ]);

        $umkm = UmkmProfile::create([
            'nik' => '640804'.str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'no_kk' => '640804'.str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'nama_pemilik' => 'Rudy Hartono',
            'nama_usaha' => 'Kerajinan Manik Dayak',
            'nomor_wa' => '081277778888',
            'kecamatan' => 'Teluk Pandan',
            'nib' => '88888888',
            'has_halal' => false,
            'has_pirt' => false,
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'umkm_id' => $umkm->id,
            'nomor_stand' => 'Stand 05',
            'kategori_jualan' => 'kriya',
            'daya_listrik_watt' => 450,
            'pin_pos' => '555555',
        ]);

        $payload = [
            'event_id' => $event->id,
            'evaluations' => [
                [
                    'stand_id' => $stand->id,
                    'cluster_kategori' => 'Established High Value',
                    'rekomendasi_program' => 'hibah_modal_apbd',
                    'skor_akhir_spk' => 92.50,
                ],
            ],
        ];

        $response = $this->postJson('/api/ml/sync-evaluations', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'synced_count' => 1,
            ]);

        $this->assertDatabaseHas('spk_umkm_evaluations', [
            'event_id' => $event->id,
            'stand_id' => $stand->id,
            'cluster_kategori' => 'Established High Value',
            'rekomendasi_program' => 'hibah_modal_apbd',
            'skor_akhir_spk' => 92.50,
        ]);

        // Cleanup
        $event->spkEvaluations()->delete();
        $stand->delete();
        $umkm->delete();
        $event->delete();
    }

    /**
     * Test public leaderboard masks financial data and sensitive identity fields.
     */
    public function test_public_leaderboard_masks_financial_and_identity_data(): void
    {
        $event = Event::create([
            'slug' => 'leaderboard-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Raya Kutai Timur 2026',
            'deskripsi' => 'Pameran Akbar Daerah',
            'penyelenggara' => 'Pemkab Kutai Timur',
            'lokasi_venue' => 'Kawasan Bukit Pelangi',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => Str::random(64),
        ]);

        $secretNik = '6408041234567899';
        $secretNoKk = '6408049876543211';
        $secretWa = '081299998888';

        $umkm = UmkmProfile::create([
            'nik' => $secretNik,
            'no_kk' => $secretNoKk,
            'nama_pemilik' => 'Haji Mansyur',
            'nama_usaha' => 'Amplang Sangatta Prima',
            'nomor_wa' => $secretWa,
            'kecamatan' => 'Sangatta Utara',
            'nib' => '99999999',
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'umkm_id' => $umkm->id,
            'nomor_stand' => 'Stand 06',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'pin_pos' => '666666',
            'total_omzet_terekam' => 15750000.00,
            'total_transaksi_terekam' => 85,
        ]);

        SpkUmkmEvaluation::create([
            'event_id' => $event->id,
            'stand_id' => $stand->id,
            'umkm_id' => $umkm->id,
            'score_konsistensi_transaksi' => 0.85,
            'score_digitalisasi_qris' => 0.60,
            'score_kepatuhan_regulasi' => 1.0,
            'skor_akhir_spk' => 88.50,
            'cluster_kategori' => 'Established High Value',
            'rekomendasi_program' => 'hibah_modal_apbd',
            'is_prioritas_hibah_200' => true,
        ]);

        $response = $this->getJson('/e/'.$event->slug.'/leaderboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'event' => ['nama_event', 'slug', 'penyelenggara', 'lokasi_venue'],
                'awarding_tier',
                'leaderboard' => [
                    '*' => [
                        'peringkat',
                        'nomor_stand',
                        'nama_usaha',
                        'poin_keaktifan',
                    ],
                ],
            ]);

        $content = $response->getContent();

        // Strict Data Leak Guard Verification:
        // NEVER expose omzet, rupiah, NIK, No KK, or phone numbers in public leaderboard
        $this->assertStringNotContainsString('total_omzet_terekam', $content);
        $this->assertStringNotContainsString('15750000', $content);
        $this->assertStringNotContainsString($secretNik, $content);
        $this->assertStringNotContainsString($secretNoKk, $content);
        $this->assertStringNotContainsString($secretWa, $content);

        // Cleanup
        $event->spkEvaluations()->delete();
        $stand->delete();
        $umkm->delete();
        $event->delete();
    }
}
