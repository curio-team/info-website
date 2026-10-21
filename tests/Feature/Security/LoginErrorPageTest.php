<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The OAuth callback forwards the `error` and `error_description` query parameters to the error page.
 * Anyone can craft such a link, so these values must be escaped.
 */
class LoginErrorPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_oauth_error_parameters_are_escaped(): void
    {
        $payload = '<script>alert(document.cookie)</script>';

        $response = $this->followingRedirects()->get('/sdclient/callback?'.http_build_query([
            'error' => $payload,
            'error_description' => $payload,
        ]));

        $response->assertOk();
        $response->assertDontSee($payload, false);
        $response->assertSee(e($payload), false);
    }
}
