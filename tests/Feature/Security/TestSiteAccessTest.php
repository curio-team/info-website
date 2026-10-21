<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The /test pages are protected by shared HTTP Basic credentials so outsiders and bots can't upload sites.
 */
class TestSiteAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.site_testing.access_user' => 'tester',
            'app.site_testing.access_password' => 'correct-horse',
        ]);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function protectedRoutes(): array
    {
        return [
            'upload form' => ['get', '/test'],
            'upload submit' => ['post', '/test'],
            'submitted page' => ['get', '/test/submitted'],
        ];
    }

    #[DataProvider('protectedRoutes')]
    public function test_requests_without_credentials_are_rejected(string $method, string $uri): void
    {
        $response = $this->{$method}($uri);

        $response->assertUnauthorized();
        $response->assertHeader('WWW-Authenticate');
    }

    #[DataProvider('protectedRoutes')]
    public function test_requests_with_a_wrong_password_are_rejected(string $method, string $uri): void
    {
        $response = $this->withBasicAuth('tester', 'wrong')->{$method}($uri);

        $response->assertUnauthorized();
    }

    public function test_requests_with_a_wrong_username_are_rejected(): void
    {
        $response = $this->withBasicAuth('someone-else', 'correct-horse')->get('/test');

        $response->assertUnauthorized();
    }

    public function test_requests_with_the_correct_credentials_are_allowed(): void
    {
        $response = $this->withBasicAuth('tester', 'correct-horse')->get('/test');

        $response->assertOk();
    }

    public function test_responses_are_not_cached(): void
    {
        $response = $this->withBasicAuth('tester', 'correct-horse')->get('/test');

        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function unconfiguredPasswords(): array
    {
        return [
            'null' => [null],
            'empty string' => [''],
        ];
    }

    #[DataProvider('unconfiguredPasswords')]
    public function test_access_is_denied_when_no_password_is_configured(mixed $configuredPassword): void
    {
        config(['app.site_testing.access_password' => $configuredPassword]);

        $this->withBasicAuth('tester', '')->get('/test')->assertUnauthorized();
        $this->withBasicAuth('tester', 'anything')->get('/test')->assertUnauthorized();
    }

    public function test_access_is_denied_when_no_username_is_configured(): void
    {
        config(['app.site_testing.access_user' => null]);

        $this->withBasicAuth('', 'correct-horse')->get('/test')->assertUnauthorized();
    }

    public function test_numeric_looking_passwords_are_compared_strictly(): void
    {
        config(['app.site_testing.access_password' => '1000']);

        // With loose comparison PHP considers "1e3" == "1000" to be true.
        $this->withBasicAuth('tester', '1e3')->get('/test')->assertUnauthorized();
        $this->withBasicAuth('tester', '1000.0')->get('/test')->assertUnauthorized();
        $this->withBasicAuth('tester', '1000')->get('/test')->assertOk();
    }
}
