<?php

namespace Tests\Feature\Security;

use App\Http\Controllers\SiteController;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Uploaded sites are extracted into the publicly served storage directory, so
 * nothing executable by the web server may end up there unless a teacher explicitly allows it.
 */
class SiteUploadSafetyTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->teacher = User::factory()->teacher()->create();
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function storeSite(UploadedFile $zip, array $overrides = []): void
    {
        $this->actingAs($this->teacher)->post(route('sites.store'), array_merge([
            'name' => 'Student',
            'year' => 1,
            'path_nl' => $zip,
        ], $overrides))->assertRedirect(route('sites.index'));
    }

    /**
     * @return list<string>
     */
    private function extractedFiles(Site $site): array
    {
        $siteDirectory = SiteController::getSitePathFromZip($site->path_nl);

        return collect(Storage::disk('public')->allFiles($siteDirectory))
            ->map(fn (string $path) => substr($path, strlen($siteDirectory)))
            ->sort()
            ->values()
            ->all();
    }

    public function test_only_allowlisted_extensions_are_extracted(): void
    {
        $this->storeSite($this->makeZipUpload([
            'index.html' => '<html><body>hi</body></html>',
            'css/style.css' => 'body {}',
            'images/kitten.JPG' => 'jpg',
            'shell.php' => '<?php phpinfo();',
            'shell.PHP' => '<?php phpinfo();',
            'shell.phtml' => '<?php phpinfo();',
            'shell.phar' => '<?php phpinfo();',
            'shell.php5' => '<?php phpinfo();',
            'page.shtml' => '<!--#exec cmd="id" -->',
            '.htaccess' => 'AddType application/x-httpd-php .html',
            'nested/.htaccess' => 'AddType application/x-httpd-php .html',
            '.user.ini' => 'auto_prepend_file=shell.html',
            'web.config' => '<configuration/>',
            'shell.cgi' => '#!/bin/sh',
            'noextension' => 'data',
        ]));

        $this->assertSame([
            'css/style.css',
            'images/kitten.JPG',
            'index.html',
        ], $this->extractedFiles(Site::sole()));
    }

    public function test_zip_entries_cannot_escape_the_site_directory(): void
    {
        $this->storeSite($this->makeZipUpload([
            'index.html' => '<html></html>',
            '../escaped-one.html' => 'x',
            '../../escaped-two.html' => 'x',
            'sub/../../escaped-three.html' => 'x',
            '/absolute.html' => 'x',
        ]));

        $site = Site::sole();
        $siteDirectory = SiteController::getSitePathFromZip($site->path_nl);
        $allFiles = Storage::disk('public')->allFiles();

        foreach ($allFiles as $file) {
            $this->assertTrue(
                $file === $site->path_nl || str_starts_with($file, $siteDirectory),
                "File [{$file}] was written outside the site directory [{$siteDirectory}]."
            );
        }

        $storageRoot = dirname(Storage::disk('public')->path(''));
        $this->assertFileDoesNotExist($storageRoot.'/escaped-one.html');
        $this->assertFileDoesNotExist($storageRoot.'/escaped-two.html');
        $this->assertFileDoesNotExist(dirname($storageRoot).'/escaped-two.html');
        $this->assertFileDoesNotExist($storageRoot.'/escaped-three.html');
    }

    public function test_symlink_entries_are_not_extracted_as_links(): void
    {
        $this->storeSite($this->makeZipUpload(
            ['index.html' => '<html></html>', 'secrets.html' => base_path('.env')],
            ['secrets.html' => 0120777],
        ));

        $sitePath = Storage::disk('public')->path(SiteController::getSitePathFromZip(Site::sole()->path_nl));

        $this->assertFalse(is_link($sitePath.'secrets.html'));
    }

    public function test_a_zip_without_allowed_files_extracts_nothing(): void
    {
        $this->storeSite($this->makeZipUpload([
            'shell.php' => '<?php phpinfo();',
            '.htaccess' => 'Options +ExecCGI',
        ]));

        $this->assertSame([], $this->extractedFiles(Site::sole()));
    }

    public function test_unsafe_files_are_only_extracted_when_explicitly_allowed(): void
    {
        $this->storeSite($this->makeZipUpload([
            'index.html' => '<html></html>',
            'contact.php' => '<?php echo "hi";',
        ]), ['allow_unsafe' => '1']);

        $this->assertSame(['contact.php', 'index.html'], $this->extractedFiles(Site::sole()));
    }

    public function test_non_zip_uploads_are_rejected(): void
    {
        $this->actingAs($this->teacher)->post(route('sites.store'), [
            'name' => 'Student',
            'year' => 1,
            'path_nl' => UploadedFile::fake()->createWithContent('shell.php', '<?php phpinfo();'),
        ])->assertSessionHasErrors('path_nl');

        $this->assertSame(0, Site::count());
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_a_php_file_renamed_to_zip_is_rejected(): void
    {
        // A real file is used, because fake uploads derive their mime type from the file name instead of the contents.
        $phpFile = tempnam(sys_get_temp_dir(), 'site');
        file_put_contents($phpFile, '<?php phpinfo();');

        $this->actingAs($this->teacher)->post(route('sites.store'), [
            'name' => 'Student',
            'year' => 1,
            'path_nl' => new UploadedFile($phpFile, 'site.zip', 'application/zip', null, true),
        ])->assertSessionHasErrors('path_nl');

        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_replacing_a_site_removes_the_old_files(): void
    {
        $this->storeSite($this->makeZipUpload(['old.html' => '<html></html>']));
        $site = Site::sole();
        $oldZip = $site->path_nl;

        $this->actingAs($this->teacher)->put(route('sites.update', $site), [
            'name' => 'Student',
            'year' => 1,
            'path_nl' => $this->makeZipUpload(['new.html' => '<html></html>']),
        ])->assertRedirect();

        Storage::disk('public')->assertMissing($oldZip);
        Storage::disk('public')->assertMissing(SiteController::getSitePathFromZip($oldZip).'old.html');
        $this->assertSame(['new.html'], $this->extractedFiles($site->fresh()));
    }

    public function test_the_upload_forms_warn_teachers_about_what_to_check(): void
    {
        $this->withoutVite();
        $site = Site::factory()->create();

        foreach ([route('sites.create'), route('sites.edit', $site)] as $url) {
            $this->actingAs($this->teacher)->get($url)
                ->assertOk()
                ->assertSee('Controleer de zip voordat je hem uploadt')
                ->assertSee('document.cookie')
                ->assertSee('.htaccess')
                ->assertSee('.user.ini');
        }
    }

    public function test_deleting_a_site_removes_its_files(): void
    {
        $this->storeSite($this->makeZipUpload(['index.html' => '<html></html>']));
        $site = Site::sole();

        $this->actingAs($this->teacher)->delete(route('sites.destroy', $site))->assertRedirect();

        $this->assertSame([], Storage::disk('public')->allFiles());
    }
}
