<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class EnvironmentSetupTest extends TestCase
{
    /**
     * Test Inertia.js Vue 3 root endpoint responds successfully.
     */
    public function test_inertia_welcome_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('status')
            ->has('version')
            ->where('status', 'Sistem PATEN Siap')
        );
    }

    /**
     * Test Supabase PostgreSQL connection and version.
     */
    public function test_supabase_postgresql_connection_is_healthy(): void
    {
        $version = DB::scalar('SELECT version()');
        $this->assertNotNull($version);
        $this->assertStringContainsString('PostgreSQL', $version);
    }

    /**
     * Test uuid-ossp extension via uuid_generate_v4().
     */
    public function test_supabase_uuid_ossp_extension_generates_valid_v4_uuid(): void
    {
        $uuid = DB::scalar('SELECT uuid_generate_v4()');
        $this->assertNotNull($uuid);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            (string) $uuid
        );
    }

    /**
     * Test all 8 tables in Supabase public schema exist and can be queried.
     */
    public function test_all_eight_core_supabase_tables_are_accessible(): void
    {
        $tables = [
            'events',
            'event_rundowns',
            'umkm_profiles',
            'event_stands',
            'stand_menus',
            'pos_transactions',
            'attendees',
            'spk_umkm_evaluations',
        ];

        foreach ($tables as $table) {
            $count = DB::table($table)->count();
            $this->assertGreaterThanOrEqual(0, $count, "Table {$table} should be queryable");
        }
    }
}
