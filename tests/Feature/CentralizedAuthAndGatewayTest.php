<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventStand;
use App\Models\User;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CentralizedAuthAndGatewayTest extends TestCase
{
    /**
     * Test User model has role and Event belongs to User.
     */
    public function test_user_has_role_and_event_belongs_to_user(): void
    {
        $user = User::create([
            'name' => 'Bambang Panitia',
            'email' => 'panitia-'.Str::lower(Str::random(6)).'@kutaitimurkab.go.id',
            'password' => bcrypt('password123'),
            'role' => 'organizer',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'organizer',
        ]);

        $event = Event::create([
            'user_id' => $user->id,
            'slug' => 'auth-test-'.Str::lower(Str::random(6)),
            'nama_event' => 'Bazar Sangatta Barat',
            'deskripsi' => 'Event uji otentikasi',
            'penyelenggara' => 'Panitia Kecamatan',
            'lokasi_venue' => 'Gelanggang Olahraga',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => Str::random(64),
        ]);

        $this->assertEquals($user->id, $event->user->id);
        $this->assertTrue($user->events->contains($event));

        // Cleanup
        $event->delete();
        $user->delete();
    }

    /**
     * Test login screen can be rendered.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->withHeaders(['X-Test-Inertia' => 'true'])->get('/login');
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
    }

    /**
     * Test users can authenticate using the login screen.
     */
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::create([
            'name' => 'Siti Panitia',
            'email' => 'siti-'.Str::lower(Str::random(6)).'@kutim.go.id',
            'password' => bcrypt('password123'),
            'role' => 'organizer',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $user->delete();
    }

    /**
     * Test users can not authenticate with invalid password.
     */
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::create([
            'name' => 'Rahmat Organizer',
            'email' => 'rahmat-'.Str::lower(Str::random(6)).'@kutim.go.id',
            'password' => bcrypt('password123'),
            'role' => 'organizer',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $user->delete();
    }

    /**
     * Test registration screen can be rendered.
     */
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->withHeaders(['X-Test-Inertia' => 'true'])->get('/register');
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page->component('Auth/Register'));
    }

    /**
     * Test new users can register.
     */
    public function test_new_users_can_register(): void
    {
        $email = 'newuser-'.Str::lower(Str::random(6)).'@kutim.go.id';

        $response = $this->post('/register', [
            'name' => 'Panitia Baru Sangatta',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'organizer',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertEquals('organizer', $user->role);

        $user->delete();
    }

    /**
     * Test users can logout.
     */
    public function test_users_can_logout(): void
    {
        $user = User::create([
            'name' => 'Logout User',
            'email' => 'logout-'.Str::lower(Str::random(6)).'@kutim.go.id',
            'password' => bcrypt('password123'),
            'role' => 'organizer',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');

        $user->delete();
    }

    /**
     * Test authenticated organizer can view dashboard with their events.
     */
    public function test_authenticated_organizer_can_view_dashboard_with_their_events(): void
    {
        $user = User::create([
            'name' => 'Pak Anto Panitia',
            'email' => 'anto-'.Str::lower(Str::random(6)).'@kutim.go.id',
            'password' => bcrypt('password123'),
            'role' => 'organizer',
        ]);

        $event = Event::create([
            'user_id' => $user->id,
            'slug' => 'dash-test-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Budaya Sangatta',
            'deskripsi' => 'Pekan budaya lokal',
            'penyelenggara' => 'Paguyuban Sangatta',
            'lokasi_venue' => 'Alun-alun Sangatta',
            'tanggal_mulai' => now()->addDays(2),
            'tanggal_selesai' => now()->addDays(5),
            'secure_management_token' => Str::random(64),
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['X-Test-Inertia' => 'true'])
            ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Organizer/Dashboard')
            ->has('events', 1)
            ->where('events.0.nama_event', 'Pekan Budaya Sangatta')
            ->has('user')
            ->has('metrics')
        );

        $event->delete();
        $user->delete();
    }

    /**
     * Test authenticated organizer can access their CMS without token query param.
     */
    public function test_authenticated_organizer_can_access_their_cms_without_token(): void
    {
        $user = User::create([
            'name' => 'Owner Panitia',
            'email' => 'owner-'.Str::lower(Str::random(6)).'@kutim.go.id',
            'password' => bcrypt('password123'),
            'role' => 'organizer',
        ]);

        $event = Event::create([
            'user_id' => $user->id,
            'slug' => 'owner-cms-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pameran Ekonomi Kreatif',
            'deskripsi' => 'Ekonomi kreatif',
            'penyelenggara' => 'Panitia Ekraf',
            'lokasi_venue' => 'Kutim Expo Center',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => Str::random(64),
        ]);

        // When logged in as owner, accessing /cms/{slug} WITHOUT token should succeed with 200
        $response = $this->actingAs($user)
            ->withHeaders(['X-Test-Inertia' => 'true'])
            ->get('/cms/'.$event->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Cms/Dashboard')
            ->where('event.nama_event', 'Pameran Ekonomi Kreatif')
        );

        $event->delete();
        $user->delete();
    }

    /**
     * Test admin dinas can access any event CMS without token.
     */
    public function test_admin_dinas_can_access_any_event_cms_without_token(): void
    {
        $dinas = User::create([
            'name' => 'Kadis Koperasi Kutim',
            'email' => 'kadis-'.Str::lower(Str::random(6)).'@kutim.go.id',
            'password' => bcrypt('password123'),
            'role' => 'admin_dinas',
        ]);

        $event = Event::create([
            'user_id' => null, // Event without direct owner or owned by someone else
            'slug' => 'dinas-cms-'.Str::lower(Str::random(6)),
            'nama_event' => 'Bazar Kuliner Akbar Kutim',
            'deskripsi' => 'Kuliner nusantara',
            'penyelenggara' => 'Dinas Koperasi Kutim',
            'lokasi_venue' => 'Bukit Pelangi',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(4),
            'secure_management_token' => Str::random(64),
        ]);

        $response = $this->actingAs($dinas)
            ->withHeaders(['X-Test-Inertia' => 'true'])
            ->get('/cms/'.$event->slug);

        $response->assertStatus(200);

        $event->delete();
        $dinas->delete();
    }

    /**
     * Test organizer creating event is assigned as owner.
     */
    public function test_organizer_creating_event_is_assigned_as_owner(): void
    {
        $user = User::create([
            'name' => 'Creator User',
            'email' => 'creator-'.Str::lower(Str::random(6)).'@kutim.go.id',
            'password' => bcrypt('password123'),
            'role' => 'organizer',
        ]);

        $payload = [
            'nama_event' => 'Bazar Kreatif Pemuda Kutim',
            'deskripsi' => 'Bazar kewirausahaan pemuda',
            'penyelenggara' => 'Komite Pemuda Kutim',
            'lokasi_venue' => 'Stadion Kudungga Sangatta',
            'tanggal_mulai' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'tanggal_selesai' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'color_mode' => 'mono',
            'color_primary' => '#132952',
            'gamifikasi_mode' => 'doorprize',
            'is_awarding_active' => true,
        ];

        $response = $this->actingAs($user)->post('/events', $payload);

        $createdEvent = Event::where('nama_event', 'Bazar Kreatif Pemuda Kutim')->first();
        $this->assertNotNull($createdEvent);
        $this->assertEquals($user->id, $createdEvent->user_id);

        $createdEvent->delete();
        $user->delete();
    }

    /**
     * Test POS gateway renders event and stand options.
     */
    public function test_pos_gateway_renders_event_and_stand_options(): void
    {
        $event = Event::create([
            'slug' => 'pos-gate-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pekan Kuliner Teluk Pandan',
            'deskripsi' => 'Kuliner lokal',
            'penyelenggara' => 'Kecamatan Teluk Pandan',
            'lokasi_venue' => 'Pantai Teluk Lombok',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(4),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand TP-01',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '987654',
        ]);

        $response = $this->withHeaders(['X-Test-Inertia' => 'true'])->get('/pos');
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Pos/Gateway')
            ->has('events')
        );

        $stand->delete();
        $event->delete();
    }

    /**
     * Test POS gateway authenticates with valid PIN and redirects to terminal.
     */
    public function test_pos_gateway_authenticates_with_valid_pin_and_redirects_to_terminal(): void
    {
        $event = Event::create([
            'slug' => 'pos-auth-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pekan UMKM Sangatta',
            'deskripsi' => 'UMKM',
            'penyelenggara' => 'Diskop UKM',
            'lokasi_venue' => 'Bukit Pelangi',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand 05',
            'kategori_jualan' => 'kuliner',
            'daya_listrik_watt' => 450,
            'pin_pos' => '334455',
        ]);

        $response = $this->post('/pos/authenticate', [
            'stand_id' => $stand->id,
            'pin_pos' => '334455',
        ]);

        $response->assertRedirect('/pos/'.$stand->id);
        $this->assertEquals($stand->id, session('pos_stand_id'));

        $stand->delete();
        $event->delete();
    }

    /**
     * Test POS gateway rejects invalid PIN.
     */
    public function test_pos_gateway_rejects_invalid_pin(): void
    {
        $event = Event::create([
            'slug' => 'pos-invalid-'.Str::lower(Str::random(6)),
            'nama_event' => 'Pekan UMKM Bengalon',
            'deskripsi' => 'UMKM',
            'penyelenggara' => 'Diskop UKM',
            'lokasi_venue' => 'Bengalon',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'secure_management_token' => Str::random(64),
        ]);

        $stand = EventStand::create([
            'event_id' => $event->id,
            'nomor_stand' => 'Stand B-01',
            'kategori_jualan' => 'agribisnis',
            'daya_listrik_watt' => 450,
            'pin_pos' => '112233',
        ]);

        $response = $this->post('/pos/authenticate', [
            'stand_id' => $stand->id,
            'pin_pos' => '999999',
        ]);

        $response->assertSessionHasErrors('pin_pos');
        $this->assertNull(session('pos_stand_id'));

        $stand->delete();
        $event->delete();
    }
}
