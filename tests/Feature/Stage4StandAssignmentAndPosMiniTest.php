<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventStand;
use App\Models\StandMenu;
use App\Models\UmkmProfile;
use Illuminate\Support\Str;
use Tests\TestCase;

class Stage4StandAssignmentAndPosMiniTest extends TestCase
{
    /**
     * Test UMKM assignment validates 16-digit NIK and KK format.
     */
    public function test_umkm_assignment_validates_nik_and_kk_format(): void
    {
        $event = Event::create([
            'slug' => 'umkm-val-'.Str::lower(Str::random(6)),
            'nama_event' => 'Paten Test Event',
            'deskripsi' => 'Test',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 01',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '123456',
        ]);

        $invalidPayload = [
            'nik' => '12345', // Invalid: not 16 digits
            'no_kk' => 'abcdefgh12345678', // Invalid: letters
            'nama_pemilik' => 'Ahmad Subandi',
            'nama_usaha' => 'Warung Barokah',
            'nomor_wa' => '081234567890',
            'kecamatan' => 'Sangatta Utara',
        ];

        $response = $this->postJson('/stands/'.$stand->id.'/assign', $invalidPayload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nik', 'no_kk']);

        // Valid 16-digit payload
        $validPayload = [
            'nik' => '640804'.str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'no_kk' => '640804'.str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'nama_pemilik' => 'Ahmad Subandi',
            'nama_usaha' => 'Warung Barokah Sangatta',
            'nomor_wa' => '081234567890',
            'kecamatan' => 'Sangatta Utara',
            'alamat_usaha' => 'Jl. Yos Sudarso II No. 14',
            'nib' => '1234567890123',
            'has_halal' => true,
            'has_pirt' => true,
            'pernah_terima_hibah' => false,
            'initial_menus' => [
                ['nama_item' => 'Nasi Bakar Cakalang', 'harga' => 25000],
                ['nama_item' => 'Es Jeruk Peras', 'harga' => 8000],
            ],
        ];

        $validResponse = $this->postJson('/stands/'.$stand->id.'/assign', $validPayload);

        $validResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('umkm_profiles', [
            'nik' => $validPayload['nik'],
            'nama_pemilik' => 'Ahmad Subandi',
            'has_halal' => true,
        ]);

        $stand->refresh();
        $this->assertNotNull($stand->umkm_id);
        $this->assertEquals(2, $stand->menus()->count());

        // Cleanup
        $stand->menus()->delete();
        $stand->delete();
        UmkmProfile::where('nik', $validPayload['nik'])->delete();
        $event->delete();
    }

    /**
     * Test POS PIN authentication establishes session and rejects invalid PIN.
     */
    public function test_pos_pin_authentication_sets_session_or_returns_error(): void
    {
        $event = Event::create([
            'slug' => 'pos-auth-'.Str::lower(Str::random(6)),
            'nama_event' => 'Paten Auth Event',
            'deskripsi' => 'Test',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 02',
            'kategori_jualan' => 'kriya',
            'daya_listrik_watt' => 450,
            'pin_pos' => '789123',
        ]);

        // 1. Wrong PIN -> 403
        $wrongResp = $this->postJson('/pos/'.$stand->id.'/login-pin', [
            'pin_pos' => '000000',
        ]);
        $wrongResp->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);

        // 2. Correct PIN -> 200 & session set
        $correctResp = $this->postJson('/pos/'.$stand->id.'/login-pin', [
            'pin_pos' => '789123',
        ]);
        $correctResp->assertStatus(200)
            ->assertJson([
                'success' => true,
                'stand_id' => $stand->id,
            ]);
        $correctResp->assertSessionHas('pos_stand_id', $stand->id);

        // Cleanup
        $stand->delete();
        $event->delete();
    }

    /**
     * Test stand menu CRUD operations under authorized session.
     */
    public function test_stand_menu_crud_operations(): void
    {
        $event = Event::create([
            'slug' => 'menu-crud-'.Str::lower(Str::random(6)),
            'nama_event' => 'Paten Menu Test',
            'deskripsi' => 'Test',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 03',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'pin_pos' => '456789',
        ]);

        // 1. CREATE menu item
        $createResp = $this->withSession(['pos_stand_id' => $stand->id])
            ->postJson('/pos/'.$stand->id.'/menus', [
                'nama_item' => 'Pisang Goreng Pontianak Crispy',
                'harga' => 15000,
                'is_available' => true,
            ]);

        $createResp->assertStatus(201)
            ->assertJson([
                'success' => true,
                'menu' => [
                    'nama_item' => 'Pisang Goreng Pontianak Crispy',
                ],
            ]);

        $menuId = $createResp->json('menu.id');
        $this->assertNotNull($menuId);

        // 2. UPDATE menu item
        $updateResp = $this->withSession(['pos_stand_id' => $stand->id])
            ->putJson('/pos/'.$stand->id.'/menus/'.$menuId, [
                'harga' => 18000,
            ]);

        $updateResp->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('stand_menus', [
            'id' => $menuId,
            'harga' => 18000.00,
        ]);

        // 3. DELETE menu item
        $deleteResp = $this->withSession(['pos_stand_id' => $stand->id])
            ->deleteJson('/pos/'.$stand->id.'/menus/'.$menuId);

        $deleteResp->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Menu berhasil dihapus.',
            ]);

        $this->assertDatabaseMissing('stand_menus', [
            'id' => $menuId,
        ]);

        // Cleanup
        $stand->delete();
        $event->delete();
    }

    /**
     * Test atomic execution of POS transaction with lockForUpdate() and omzet increments.
     */
    public function test_pos_transaction_atomic_execution(): void
    {
        $event = Event::create([
            'slug' => 'pos-tx-'.Str::lower(Str::random(6)),
            'nama_event' => 'Paten POS Tx Test',
            'deskripsi' => 'Test',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 04',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'pin_pos' => '321654',
            'total_omzet_terekam' => 0.00,
            'total_transaksi_terekam' => 0,
        ]);

        $menu = StandMenu::create([
            'stand_id' => $stand->id,
            'nama_item' => 'Sate Payau Khas Kutim',
            'harga' => 35000.00,
            'is_available' => true,
        ]);

        // Post transaction for 3 items (subtotal = 3 * 35000 = 105000)
        $txPayload = [
            'menu_id' => $menu->id,
            'qty' => 3,
            'metode_pembayaran' => 'qris',
        ];

        $response = $this->withSession(['pos_stand_id' => $stand->id])
            ->postJson('/pos/'.$stand->id.'/transactions', $txPayload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'subtotal' => 105000.00,
                'total_omzet_stand' => 105000.00,
                'total_transaksi_stand' => 1,
            ]);

        $this->assertDatabaseHas('pos_transactions', [
            'event_id' => $event->id,
            'stand_id' => $stand->id,
            'menu_id' => $menu->id,
            'qty' => 3,
            'subtotal' => 105000.00,
            'metode_pembayaran' => 'qris',
        ]);

        $stand->refresh();
        $this->assertEquals(105000.00, (float) $stand->total_omzet_terekam);
        $this->assertEquals(1, $stand->total_transaksi_terekam);

        // Cleanup
        $stand->posTransactions()->delete();
        $stand->menus()->delete();
        $stand->delete();
        $event->delete();
    }

    /**
     * Test POS transaction prevents unauthorized tampering (session mismatch or cross-stand menu).
     */
    public function test_pos_transaction_prevents_unauthorized_stand_tampering(): void
    {
        $event = Event::create([
            'slug' => 'pos-tamper-'.Str::lower(Str::random(6)),
            'nama_event' => 'Paten Tamper Test',
            'deskripsi' => 'Test',
            'penyelenggara' => 'Panitia',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
        ]);

        $standA = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand A',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '111222',
        ]);

        $standB = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand B',
            'kategori_jualan' => 'fesyen',
            'daya_listrik_watt' => 450,
            'pin_pos' => '333444',
        ]);

        $menuB = StandMenu::create([
            'stand_id' => $standB->id,
            'nama_item' => 'Batik Motif Kutai Timur',
            'harga' => 150000.00,
            'is_available' => true,
        ]);

        // 1. Session Stand A tries to record transaction on Stand B URL -> 403 Forbidden
        $mismatchResp = $this->withSession(['pos_stand_id' => $standA->id])
            ->postJson('/pos/'.$standB->id.'/transactions', [
                'menu_id' => $menuB->id,
                'qty' => 1,
                'metode_pembayaran' => 'cash',
            ]);
        $mismatchResp->assertStatus(403);

        // 2. Stand A tries to submit menu belonging to Stand B -> 422 Unprocessable
        $tamperedMenuResp = $this->withSession(['pos_stand_id' => $standA->id])
            ->postJson('/pos/'.$standA->id.'/transactions', [
                'menu_id' => $menuB->id, // Menu B belongs to Stand B, not Stand A
                'qty' => 1,
                'metode_pembayaran' => 'cash',
            ]);
        $tamperedMenuResp->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Item menu tidak valid untuk stand ini.',
            ]);

        // Cleanup
        $standB->menus()->delete();
        $standA->delete();
        $standB->delete();
        $event->delete();
    }

    /**
     * Test menu export endpoint includes induk event theme colors and menu records.
     */
    public function test_menu_export_includes_induk_event_colors(): void
    {
        $event = Event::create([
            'slug' => 'export-menu-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Budaya Sangatta',
            'deskripsi' => 'Pesta seni dan UMKM',
            'penyelenggara' => 'Disporapar',
            'lokasi_venue' => 'Kawasan Bukit Pelangi',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
            'color_mode' => 'duo',
            'color_primary' => '#0F766E',
            'color_secondary' => '#F59E0B',
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 05',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '555666',
        ]);

        StandMenu::create([
            'stand_id' => $stand->id,
            'nama_item' => 'Amplang Khas Bengalon',
            'harga' => 20000.00,
            'is_available' => true,
        ]);

        $response = $this->getJson('/pos/'.$stand->id.'/export-menu');

        $response->assertStatus(200)
            ->assertJson([
                'stand' => [
                    'nomor_stand' => 'Stand 05',
                    'kategori_jualan' => 'kuliner',
                ],
                'event' => [
                    'nama_event' => 'Pekan Budaya Sangatta',
                    'color_primary' => '#0F766E',
                    'color_secondary' => '#F59E0B',
                ],
            ])
            ->assertJsonStructure([
                'stand',
                'event',
                'menus',
            ]);

        // Cleanup
        $stand->menus()->delete();
        $stand->delete();
        $event->delete();
    }
}
