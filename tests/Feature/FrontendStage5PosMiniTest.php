<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventStand;
use App\Models\StandMenu;
use App\Models\UmkmProfile;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FrontendStage5PosMiniTest extends TestCase
{
    /**
     * Test Stand Assignment page renders Inertia Stands/Assign with required props.
     */
    public function test_stand_assign_page_renders_inertia_component_with_expected_props(): void
    {
        $event = Event::create([
            'slug' => 'stage5-assign-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Raya Sangatta Utara 2026',
            'deskripsi' => 'Bazar Kuliner & Kriya UMKM',
            'penyelenggara' => 'Dinas Koperasi & UKM Kab. Kutai Timur',
            'lokasi_venue' => 'Kawasan Pusat Pemerintahan Bukit Pelangi',
            'tanggal_mulai' => now()->addDays(2),
            'tanggal_selesai' => now()->addDays(5),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 01',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '654321',
        ]);

        $response = $this->withHeaders(['X-Test-Inertia' => 'true'])
            ->get('/stands/'.$stand->id.'/assign');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Stands/Assign')
            ->has('stand')
            ->where('stand.nomor_stand', 'Stand 01')
            ->has('event')
            ->where('event.nama_event', 'Pekan Raya Sangatta Utara 2026')
            ->has('disclaimer')
        );

        // Cleanup
        $stand->delete();
        $event->delete();
    }

    /**
     * Test submitting stand assignment stores UMKM profile and redirects to POS login.
     */
    public function test_stand_assign_submits_and_redirects_to_pos_login(): void
    {
        $event = Event::create([
            'slug' => 'stage5-submit-'.Str::lower(Str::random(6)),
            'nama_event' => 'Bazar Pembangunan Sangatta',
            'deskripsi' => 'Event Resmi',
            'penyelenggara' => 'Pemkab Kutim',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 02',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'pin_pos' => '112233',
        ]);

        $randomDigits = str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);
        $nik = '640804'.$randomDigits;
        $noKk = '640804'.$randomDigits;

        $payload = [
            'nik' => $nik,
            'no_kk' => $noKk,
            'nama_pemilik' => 'Hj. Siti Rahmah',
            'nama_usaha' => 'Dapur Kutim Berkah',
            'nomor_wa' => '081234567891',
            'kecamatan' => 'Sangatta Utara',
            'alamat_usaha' => 'Jl. Diponegoro Gang 3 No. 12',
            'nib' => '9120001234567',
            'has_halal' => true,
            'has_pirt' => true,
            'pernah_terima_hibah' => true,
            'tahun_terima_hibah' => 2024,
            'initial_menus' => [
                ['nama_item' => 'Nasi Bakar Cakalang Sangatta', 'harga' => 20000],
                ['nama_item' => 'Jus Buah Naga Kutim', 'harga' => 10000],
            ],
        ];

        $response = $this->withHeaders(['X-Inertia' => 'true'])
            ->post('/stands/'.$stand->id.'/assign', $payload);

        $response->assertStatus(302);
        $response->assertRedirect('/pos/'.$stand->id.'/login');

        $this->assertDatabaseHas('umkm_profiles', [
            'nik' => $nik,
            'no_kk' => $noKk,
            'nama_usaha' => 'Dapur Kutim Berkah',
            'kecamatan' => 'Sangatta Utara',
            'has_halal' => true,
        ]);

        $this->assertDatabaseHas('stand_menus', [
            'stand_id' => $stand->id,
            'nama_item' => 'Nasi Bakar Cakalang Sangatta',
            'harga' => 20000,
        ]);

        // Cleanup
        $stand->menus()->delete();
        $stand->delete();
        UmkmProfile::where('nik', $nik)->delete();
        $event->delete();
    }

    /**
     * Test POS login page renders Inertia Pos/Login with stand and event props.
     */
    public function test_pos_login_page_renders_inertia_component(): void
    {
        $event = Event::create([
            'slug' => 'stage5-login-'.Str::lower(Str::random(6)),
            'nama_event' => 'Festival Kuliner Nusantara',
            'deskripsi' => 'Festival',
            'penyelenggara' => 'Diskop-UKM',
            'lokasi_venue' => 'Kutim',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 03',
            'kategori_jualan' => 'kriya',
            'daya_listrik_watt' => 450,
            'pin_pos' => '998877',
        ]);

        $response = $this->withHeaders(['X-Test-Inertia' => 'true'])
            ->get('/pos/'.$stand->id.'/login');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Pos/Login')
            ->has('stand')
            ->where('stand.nomor_stand', 'Stand 03')
            ->has('event')
            ->where('event.nama_event', 'Festival Kuliner Nusantara')
        );

        // Cleanup
        $stand->delete();
        $event->delete();
    }

    /**
     * Test POS Terminal renders Inertia Pos/Terminal when authenticated with PIN.
     */
    public function test_pos_terminal_renders_inertia_component_under_authenticated_session(): void
    {
        $event = Event::create([
            'slug' => 'stage5-terminal-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pameran Sangatta Expo',
            'deskripsi' => 'Expo',
            'penyelenggara' => 'Pemkab',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 04',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'pin_pos' => '334455',
        ]);

        StandMenu::create([
            'stand_id' => $stand->id,
            'nama_item' => 'Es Cendol Sawit Kutim',
            'harga' => 12000,
            'is_available' => true,
        ]);

        // Login with correct PIN
        $loginResp = $this->withSession([])
            ->post('/pos/'.$stand->id.'/login-pin', ['pin_pos' => '334455']);

        $loginResp->assertStatus(302);
        $loginResp->assertRedirect('/pos/'.$stand->id);
        $loginResp->assertSessionHas('pos_stand_id', $stand->id);

        // Now visit Terminal with authenticated session
        $terminalResp = $this->withSession(['pos_stand_id' => $stand->id])
            ->withHeaders(['X-Test-Inertia' => 'true'])
            ->get('/pos/'.$stand->id);

        $terminalResp->assertStatus(200);
        $terminalResp->assertInertia(fn (Assert $page) => $page
            ->component('Pos/Terminal')
            ->has('stand')
            ->where('stand.nomor_stand', 'Stand 04')
            ->has('event')
            ->has('menus', 1)
            ->where('menus.0.nama_item', 'Es Cendol Sawit Kutim')
            ->has('recent_transactions')
        );

        // Cleanup
        $stand->menus()->delete();
        $stand->delete();
        $event->delete();
    }

    /**
     * Test POS Terminal records atomic transaction and redirects back.
     */
    public function test_pos_terminal_records_transaction_successfully(): void
    {
        $event = Event::create([
            'slug' => 'stage5-tx-'.Str::lower(Str::random(6)),
            'nama_event' => 'Kutim Culinary Fest',
            'deskripsi' => 'Fest',
            'penyelenggara' => 'Dispar',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 05',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'pin_pos' => '778899',
            'total_omzet_terekam' => 0.00,
            'total_transaksi_terekam' => 0,
        ]);

        $menu = StandMenu::create([
            'stand_id' => $stand->id,
            'nama_item' => 'Ayam Cincane Sangatta',
            'harga' => 30000,
            'is_available' => true,
        ]);

        $txPayload = [
            'menu_id' => $menu->id,
            'qty' => 2,
            'metode_pembayaran' => 'cash',
        ];

        $response = $this->withSession(['pos_stand_id' => $stand->id])
            ->withHeaders(['X-Inertia' => 'true'])
            ->post('/pos/'.$stand->id.'/transactions', $txPayload);

        $response->assertStatus(302);
        $response->assertRedirect('/pos/'.$stand->id);

        $this->assertDatabaseHas('pos_transactions', [
            'stand_id' => $stand->id,
            'menu_id' => $menu->id,
            'qty' => 2,
            'subtotal' => 60000.00,
            'metode_pembayaran' => 'cash',
        ]);

        $stand->refresh();
        $this->assertEquals(60000.00, (float) $stand->total_omzet_terekam);
        $this->assertEquals(1, $stand->total_transaksi_terekam);

        // Cleanup
        $stand->posTransactions()->delete();
        $stand->menus()->delete();
        $stand->delete();
        $event->delete();
    }

    /**
     * Test printable menu sheet renders Inertia Pos/ExportMenu with required props.
     */
    public function test_export_menu_sheet_renders_inertia_component(): void
    {
        $event = Event::create([
            'slug' => 'stage5-export-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pesta UMKM Bukit Pelangi',
            'deskripsi' => 'Pesta',
            'penyelenggara' => 'Setda Kutim',
            'lokasi_venue' => 'Bukit Pelangi',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(2),
            'secure_management_token' => Str::random(64),
            'color_primary' => '#132952',
            'color_secondary' => '#00B262',
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 06',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '554433',
        ]);

        StandMenu::create([
            'stand_id' => $stand->id,
            'nama_item' => 'Kerupuk Ikan Sangkulirang',
            'harga' => 15000,
            'is_available' => true,
        ]);

        $response = $this->withHeaders(['X-Test-Inertia' => 'true'])
            ->get('/pos/'.$stand->id.'/export-menu');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Pos/ExportMenu')
            ->has('stand')
            ->where('stand.nomor_stand', 'Stand 06')
            ->has('event')
            ->where('event.nama_event', 'Pesta UMKM Bukit Pelangi')
            ->has('menus', 1)
            ->where('menus.0.nama_item', 'Kerupuk Ikan Sangkulirang')
        );

        // Cleanup
        $stand->menus()->delete();
        $stand->delete();
        $event->delete();
    }
}
