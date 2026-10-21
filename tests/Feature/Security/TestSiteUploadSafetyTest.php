<?php

namespace Tests\Feature\Security;

use App\Jobs\RemoveTestSite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Students upload test sites through /test. These must never be able to contain executable files.
 */
class TestSiteUploadSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('public');
        Queue::fake();

        config([
            'app.site_testing.access_user' => 'tester',
            'app.site_testing.access_password' => 'correct-horse',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function submitTestSite(array $data): TestResponse
    {
        return $this->withBasicAuth('tester', 'correct-horse')->post(route('sites.test.submit'), $data);
    }

    public function test_unsafe_files_are_never_extracted_even_when_requested(): void
    {
        $this->submitTestSite([
            'path' => $this->makeZipUpload([
                'index.html' => '<html><body>hi</body></html>',
                'shell.php' => '<?php phpinfo();',
                '.htaccess' => 'AddType application/x-httpd-php .html',
            ]),
            'allow_unsafe' => '1',
        ])->assertRedirect(route('sites.test.submitted'));

        $files = Storage::disk('public')->allFiles('temp');

        $this->assertCount(1, array_filter($files, fn (string $file) => str_ends_with($file, '/index.html')));
        $this->assertSame([], array_filter($files, fn (string $file) => str_contains($file, 'shell.php') || str_contains($file, '.htaccess')));
    }

    public function test_test_sites_are_scheduled_for_removal(): void
    {
        config(['app.site_testing.remove_after' => 5]);

        $this->submitTestSite([
            'path' => $this->makeZipUpload(['index.html' => '<html><body></body></html>']),
        ])->assertRedirect();

        Queue::assertPushed(RemoveTestSite::class, fn (RemoveTestSite $job) => str_starts_with($job->zipPath, 'temp/')
            && str_starts_with($job->sitePath, 'temp/')
            && $job->delay !== null);
    }

    public function test_the_test_results_are_injected_into_extracted_html_files(): void
    {
        $this->submitTestSite([
            'path' => $this->makeZipUpload([
                'index.html' => '<html><body></body></html>',
            ]),
        ])->assertRedirect();

        $index = collect(Storage::disk('public')->allFiles('temp'))
            ->first(fn (string $file) => str_ends_with($file, '/index.html'));

        $this->assertStringContainsString('__sdTestResults', Storage::disk('public')->get($index));
    }

    public function test_extracted_file_names_cannot_inject_markup_into_the_test_results(): void
    {
        // Rendered directly, because these file names are not valid on every filesystem.
        $rendered = view('app.sites.partials.site-tests', [
            'extracted' => ['</script><script>alert(1)</script>.html', "x';alert(1);'.html", 'x"+alert(1)+".html'],
        ])->render();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $rendered);
        $this->assertStringNotContainsString("x';alert(1);'", $rendered);
        $this->assertStringNotContainsString('x"+alert(1)+"', $rendered);
    }

    public function test_a_file_is_required(): void
    {
        $this->submitTestSite([])->assertSessionHasErrors('path');

        Queue::assertNothingPushed();
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_non_zip_uploads_are_rejected(): void
    {
        $this->submitTestSite([
            'path' => UploadedFile::fake()->createWithContent('shell.php', '<?php phpinfo();'),
        ])->assertSessionHasErrors('path');

        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_a_corrupt_zip_does_not_crash(): void
    {
        // Starts with the zip magic bytes, so it passes the mime check, but cannot be opened.
        $this->submitTestSite([
            'path' => UploadedFile::fake()->createWithContent('site.zip', "PK\x03\x04".str_repeat("\0", 64)),
        ])->assertSessionHasErrors('path');
    }

    public function test_the_submitted_page_without_a_test_site_returns_not_found(): void
    {
        $this->withBasicAuth('tester', 'correct-horse')
            ->get(route('sites.test.submitted'))
            ->assertNotFound();
    }
}
