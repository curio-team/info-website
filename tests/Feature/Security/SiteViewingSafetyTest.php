<?php

namespace Tests\Feature\Security;

use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Student sites are shown inside a sandboxed iframe so their scripts can't access our cookies or pages.
 */
class SiteViewingSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function assertSandboxedFrame(string $html): void
    {
        $this->assertMatchesRegularExpression('/<iframe[^>]*\ssandbox="([^"]*)"/', $html);

        preg_match('/<iframe[^>]*\ssandbox="([^"]*)"/', $html, $matches);
        $permissions = preg_split('/\s+/', trim($matches[1]));

        $this->assertNotContains('allow-same-origin', $permissions);
        $this->assertNotContains('allow-top-navigation', $permissions);
        $this->assertNotContains('allow-modals', $permissions);
    }

    public function test_sites_are_rendered_in_a_sandboxed_iframe(): void
    {
        $site = Site::factory()->withEnglish()->create();

        $this->assertSandboxedFrame($this->get(route('home'))->assertOk()->getContent());
        $this->assertSandboxedFrame($this->get(route('home.english'))->assertOk()->getContent());
        $this->assertSandboxedFrame($this->get(route('sites.show', $site))->assertOk()->getContent());
        $this->assertSandboxedFrame($this->get(route('sites.show.english', $site))->assertOk()->getContent());
    }

    public function test_the_english_page_of_a_dutch_only_site_is_not_found(): void
    {
        $site = Site::factory()->create();

        $this->get(route('sites.show.english', $site))->assertNotFound();
    }

    public function test_student_names_are_escaped(): void
    {
        $site = Site::factory()->create(['name' => '<script>alert(1)</script>']);

        $response = $this->get(route('sites.show', $site));

        $response->assertOk();
        $response->assertDontSee('<script>alert(1)</script>', false);
        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_the_frame_points_to_the_public_storage_of_the_site(): void
    {
        $site = Site::factory()->create(['path_nl' => 'abc.zip']);

        $this->get(route('sites.show', $site))->assertSee('src="/storage/abc/"', false);
    }
}
