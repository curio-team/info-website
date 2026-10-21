<?php

namespace Tests\Feature\Security;

use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Only teachers (and admins) may manage the published sites.
 */
class SiteManagementAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('public');
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function managementRoutes(): array
    {
        return [
            'index' => ['get', 'sites.index'],
            'create' => ['get', 'sites.create'],
            'store' => ['post', 'sites.store'],
            'edit' => ['get', 'sites.edit'],
            'update' => ['put', 'sites.update'],
            'destroy' => ['delete', 'sites.destroy'],
        ];
    }

    #[DataProvider('managementRoutes')]
    public function test_guests_are_redirected_to_login(string $method, string $routeName): void
    {
        $site = Site::factory()->create();

        $response = $this->{$method}(route($routeName, $site));

        $response->assertRedirect(route('login'));
        $this->assertModelExists($site);
    }

    #[DataProvider('managementRoutes')]
    public function test_students_are_forbidden(string $method, string $routeName): void
    {
        $student = User::factory()->student()->create();
        $site = Site::factory()->create();

        $response = $this->actingAs($student)->{$method}(route($routeName, $site), [
            'name' => 'Hacked',
            'year' => 1,
            'path_nl' => $this->makeZipUpload(['index.html' => '<html></html>']),
            'allow_unsafe' => true,
        ]);

        $response->assertForbidden();
        $this->assertModelExists($site);
        $this->assertSame(1, Site::count());
        $this->assertNotSame('Hacked', $site->fresh()->name);
    }

    public function test_students_cannot_upload_unsafe_sites(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student)->post(route('sites.store'), [
            'name' => 'Shell',
            'year' => 1,
            'path_nl' => $this->makeZipUpload(['shell.php' => '<?php system($_GET["c"]);']),
            'allow_unsafe' => '1',
        ])->assertForbidden();

        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function staffTypes(): array
    {
        return [
            'teacher' => ['teacher'],
            'admin' => ['admin'],
        ];
    }

    #[DataProvider('staffTypes')]
    public function test_staff_can_view_the_management_pages(string $type): void
    {
        $staff = User::factory()->state(['type' => $type])->create();
        $site = Site::factory()->create();

        $this->actingAs($staff)->get(route('sites.index'))->assertOk();
        $this->actingAs($staff)->get(route('sites.create'))->assertOk();
        $this->actingAs($staff)->get(route('sites.edit', $site))->assertOk();
    }

    #[DataProvider('staffTypes')]
    public function test_staff_can_delete_sites(string $type): void
    {
        $staff = User::factory()->state(['type' => $type])->create();
        $site = Site::factory()->create();

        $this->actingAs($staff)->delete(route('sites.destroy', $site))->assertRedirect(route('sites.index'));

        $this->assertModelMissing($site);
    }

    public function test_publisher_cannot_be_spoofed_when_storing(): void
    {
        $teacher = User::factory()->teacher()->create();
        $otherTeacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)->post(route('sites.store'), [
            'name' => 'Student',
            'year' => 1,
            'path_nl' => $this->makeZipUpload(['index.html' => '<html></html>']),
            'publisher_id' => $otherTeacher->id,
        ])->assertRedirect(route('sites.index'));

        $this->assertSame($teacher->id, Site::sole()->publisher_id);
    }

    public function test_publisher_cannot_be_changed_when_updating(): void
    {
        $teacher = User::factory()->teacher()->create();
        $otherTeacher = User::factory()->teacher()->create();
        $site = Site::factory()->for($teacher, 'publisher')->create();

        $this->actingAs($teacher)->put(route('sites.update', $site), [
            'name' => 'Student',
            'year' => 1,
            'path_nl' => $this->makeZipUpload(['index.html' => '<html></html>']),
            'publisher_id' => $otherTeacher->id,
        ])->assertRedirect();

        $this->assertSame($teacher->id, $site->fresh()->publisher_id);
    }
}
