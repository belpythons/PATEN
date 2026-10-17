<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FrontendStage1DesignSystemTest extends TestCase
{
    /**
     * Test welcome page renders with Inertia Welcome component and status props.
     */
    public function test_welcome_page_renders_with_inertia_and_correct_props(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('status')
            ->has('version')
            ->where('status', 'Sistem PATEN Siap')
            ->where('version', '1.0.0')
        );
    }

    /**
     * Test that HTML response includes Hallmark Cobalt Google Fonts.
     */
    public function test_html_includes_hallmark_cobalt_google_fonts(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Space+Grotesk', false);
        $response->assertSee('Inter', false);
        $response->assertSee('JetBrains+Mono', false);
    }

    /**
     * Test that Vite manifest loads bundled assets properly.
     */
    public function test_vite_assets_are_loaded_in_head(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertTrue(
            str_contains($content, 'build/assets/app') || str_contains($content, 'resources/js/app.js'),
            'Vite assets (either production build or dev server) should be loaded in <head>'
        );
    }

    /**
     * Test health check route remains fully functional.
     */
    public function test_health_check_endpoint_remains_healthy(): void
    {
        $response = $this->getJson('/health');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'healthy',
        ]);
    }
}
