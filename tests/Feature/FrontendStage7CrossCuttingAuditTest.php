<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventStand;
use App\Models\StandMenu;
use App\Models\UmkmProfile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FrontendStage7CrossCuttingAuditTest extends TestCase
{
    /**
     * Test Vite production manifest exists and contains valid CSS and JS entries.
     */
    public function test_vite_manifest_exists_and_contains_valid_assets(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $this->assertFileExists($manifestPath, 'Vite manifest.json must exist in public/build.');

        $manifestContent = json_decode(File::get($manifestPath), true);
        $this->assertIsArray($manifestContent);

        // Verify CSS entry
        $this->assertArrayHasKey('resources/css/app.css', $manifestContent);
        $cssFile = public_path('build/'.$manifestContent['resources/css/app.css']['file']);
        $this->assertFileExists($cssFile, 'Compiled CSS file must exist in public/build.');

        // Verify JS entry
        $this->assertArrayHasKey('resources/js/app.js', $manifestContent);
        $jsFile = public_path('build/'.$manifestContent['resources/js/app.js']['file']);
        $this->assertFileExists($jsFile, 'Compiled JS bundle must exist in public/build.');
    }

    /**
     * Test all 11 core Inertia Vue page files exist in the repository.
     */
    public function test_all_core_inertia_vue_pages_exist(): void
    {
        $requiredPages = [
            'resources/js/Pages/Welcome.vue',
            'resources/js/Pages/Events/Create.vue',
            'resources/js/Pages/Events/Show.vue',
            'resources/js/Pages/Events/Leaderboard.vue',
            'resources/js/Pages/Events/PosterExport.vue',
            'resources/js/Pages/Cms/Dashboard.vue',
            'resources/js/Pages/Cms/Evaluations.vue',
            'resources/js/Pages/Stands/Assign.vue',
            'resources/js/Pages/Pos/Login.vue',
            'resources/js/Pages/Pos/Terminal.vue',
            'resources/js/Pages/Pos/ExportMenu.vue',
        ];

        foreach ($requiredPages as $pagePath) {
            $fullPath = base_path($pagePath);
            $this->assertFileExists($fullPath, "Page component {$pagePath} must exist.");
        }
    }

    /**
     * Test CSS design tokens enforce Triad color system and WCAG accessibility rules.
     */
    public function test_css_tokens_enforce_triad_palette_and_reduced_motion(): void
    {
        $cssPath = resource_path('css/app.css');
        $this->assertFileExists($cssPath);

        $cssContent = File::get($cssPath);

        // 1. Strict Triad Brand Colors
        $this->assertStringContainsString('#132952', $cssContent, 'Deep Navy Blue (#132952) must be present.');
        $this->assertStringContainsString('#00B262', $cssContent, 'Vibrant Emerald Green (#00B262) must be present.');
        $this->assertStringContainsString('#FAA21B', $cssContent, 'Warm Amber Orange (#FAA21B) must be present.');

        // 2. Hallmark Font Pairing
        $this->assertStringContainsString('Space Grotesk', $cssContent, 'Display typography Space Grotesk must be defined.');
        $this->assertStringContainsString('JetBrains Mono', $cssContent, 'Monospace typography JetBrains Mono must be defined.');
        $this->assertStringContainsString('Inter', $cssContent, 'Body typography Inter must be defined.');

        // 3. WCAG AA prefers-reduced-motion accessibility rule
        $this->assertStringContainsString('prefers-reduced-motion', $cssContent, 'prefers-reduced-motion media query must be present.');
    }

    /**
     * Test public customer journey endpoints render correctly across desktop and mobile.
     */
    public function test_public_customer_journey_renders_all_pages_cleanly(): void
    {
        $event = Event::create([
            'slug' => 'audit-public-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Budaya Sangatta 2026',
            'deskripsi' => 'Event Akbar UMKM Kutim',
            'penyelenggara' => 'Dinas Koperasi & UKM Kutai Timur',
            'lokasi_venue' => 'Kawasan Pusat Pemerintahan Bukit Pelangi',
            'tanggal_mulai' => now()->addDays(2),
            'tanggal_selesai' => now()->addDays(5),
            'secure_management_token' => Str::random(64),
            'color_primary' => '#132952',
            'color_secondary' => '#00B262',
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 01',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '123456',
        ]);

        // 1. Welcome Landing Page
        $this->flushHeaders();
        $welcomeResp = $this->withHeaders(['X-Test-Inertia' => 'true'])->get('/');
        $welcomeResp->assertStatus(200);
        $welcomeResp->assertInertia(fn (Assert $page) => $page->component('Welcome'));

        // 2. Event Creation Wizard
        $this->flushHeaders();
        $createResp = $this->withHeaders(['X-Test-Inertia' => 'true'])->get('/events/create');
        $createResp->assertStatus(200);
        $createResp->assertInertia(fn (Assert $page) => $page->component('Events/Create'));

        // 3. Public Event Microsite
        $this->flushHeaders();
        $showResp = $this->withHeaders(['X-Test-Inertia' => 'true'])->get('/e/'.$event->slug);
        $showResp->assertStatus(200);
        $showResp->assertInertia(fn (Assert $page) => $page
            ->component('Events/Show')
            ->has('event')
            ->where('event.nama_event', 'Pekan Budaya Sangatta 2026')
        );

        // 4. Public Leaderboard
        $this->flushHeaders();
        $leaderboardResp = $this->withHeaders(['X-Test-Inertia' => 'true'])->get('/e/'.$event->slug.'/leaderboard');
        $leaderboardResp->assertStatus(200);
        $leaderboardResp->assertInertia(fn (Assert $page) => $page
            ->component('Events/Leaderboard')
            ->has('event')
        );

        // 5. Printable Poster Export
        $this->flushHeaders();
        $posterResp = $this->withHeaders(['X-Test-Inertia' => 'true'])->get('/events/'.$event->slug.'/poster-export');
        $posterResp->assertStatus(200);
        $posterResp->assertInertia(fn (Assert $page) => $page
            ->component('Events/PosterExport')
            ->has('event')
        );

        // Cleanup
        $stand->delete();
        $event->delete();
    }

    /**
     * Test complete operational lifecycle: Stand Assignment -> POS Login -> POS Transaction -> CMS Evaluation.
     */
    public function test_complete_operational_lifecycle_integration(): void
    {
        $token = Str::random(64);
        $event = Event::create([
            'slug' => 'lifecycle-audit-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Raya Expo Sangatta',
            'deskripsi' => 'Audit Integrasi Terpadu',
            'penyelenggara' => 'Diskop-UKM Kutim',
            'lokasi_venue' => 'Sangatta',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => $token,
            'color_primary' => '#132952',
            'color_secondary' => '#00B262',
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 10',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 900,
            'pin_pos' => '654321',
        ]);

        // Step 1: Stand Assignment (Perbup Profiling)
        $random = str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);
        $nik = '640804'.$random;
        $noKk = '640804'.$random;

        $assignPayload = [
            'nik' => $nik,
            'no_kk' => $noKk,
            'nama_pemilik' => 'Ibu Fatimah',
            'nama_usaha' => 'Warung Amplang Sangatta',
            'nomor_wa' => '081234567890',
            'kecamatan' => 'Sangatta Utara',
            'alamat_usaha' => 'Jl. Pendidikan No. 5',
            'nib' => '1234567890123',
            'has_halal' => true,
            'has_pirt' => true,
            'pernah_terima_hibah' => false,
            'initial_menus' => [
                ['nama_item' => 'Amplang Khas Kutim', 'harga' => 25000],
            ],
        ];

        $assignResp = $this->withHeaders(['X-Inertia' => 'true'])
            ->post('/stands/'.$stand->id.'/assign', $assignPayload);

        $assignResp->assertStatus(302);
        $assignResp->assertRedirect('/pos/'.$stand->id.'/login');

        // Step 2: POS Login with PIN
        $loginResp = $this->withSession([])
            ->post('/pos/'.$stand->id.'/login-pin', ['pin_pos' => '654321']);

        $loginResp->assertStatus(302);
        $loginResp->assertRedirect('/pos/'.$stand->id);
        $loginResp->assertSessionHas('pos_stand_id', $stand->id);

        // Step 3: Record fast POS transaction
        $menu = StandMenu::where('stand_id', $stand->id)->first();
        $this->assertNotNull($menu);

        $txResp = $this->withSession(['pos_stand_id' => $stand->id])
            ->withHeaders(['X-Inertia' => 'true'])
            ->post('/pos/'.$stand->id.'/transactions', [
                'menu_id' => $menu->id,
                'qty' => 4,
                'metode_pembayaran' => 'qris',
            ]);

        $txResp->assertStatus(302);
        $txResp->assertRedirect('/pos/'.$stand->id);

        $stand->refresh();
        $this->assertEquals(100000.00, (float) $stand->total_omzet_terekam);
        $this->assertEquals(1, $stand->total_transaksi_terekam);

        // Step 4: CMS SPK Evaluation
        $evalResp = $this->withHeaders(['X-Inertia' => 'true'])
            ->post('/cms/'.$event->slug.'/evaluate-spk?token='.$token);

        $evalResp->assertStatus(302);
        $evalResp->assertRedirect('/cms/'.$event->slug.'/evaluations?token='.$token);

        // Step 5: Verify CMS Evaluations Page
        $this->flushHeaders();
        $evalPageResp = $this->withHeaders(['X-Test-Inertia' => 'true'])
            ->get('/cms/'.$event->slug.'/evaluations?token='.$token);

        $evalPageResp->assertStatus(200);
        $evalPageResp->assertInertia(fn (Assert $page) => $page
            ->component('Cms/Evaluations')
            ->has('evaluations', 1)
            ->where('summary.total_evaluated', 1)
        );

        // Cleanup
        $event->spkEvaluations()->delete();
        $stand->posTransactions()->delete();
        $stand->menus()->delete();
        $stand->delete();
        UmkmProfile::where('nik', $nik)->delete();
        $event->delete();
    }
}
