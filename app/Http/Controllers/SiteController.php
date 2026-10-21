<?php

namespace App\Http\Controllers;

use App\Http\Requests\SiteStoreRequest;
use App\Http\Requests\SiteUpdateRequest;
use App\Jobs\RemoveTestSite;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class SiteController extends Controller
{
    public const EXT_ALLOWLIST = [
        'html',
        'css',
        'js',
        'json',
        'png',
        'jpg',
        'jpeg',
        'gif',
        'svg',
        'webp',
        'pdf',
        'woff',
        'woff2',
        'ttf',
    ];

    /**
     * @return Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('viewAny', Site::class)) {
            abort(403);
        }

        $search = $request->get('search', '');

        $sites = Site::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.sites.index', compact('sites', 'search'));
    }

    /**
     * @return Response
     */
    public function create(Request $request)
    {
        if (! Gate::allows('create', Site::class)) {
            abort(403);
        }

        return view('app.sites.create');
    }

    /**
     * @return string
     */
    public static function getSitePathFromZip(string $zipPath)
    {
        return rtrim($zipPath, '.zip').'/';
    }

    /**
     * @param  string  $path
     * @return bool
     */
    private static function removeSite(string $zipPath)
    {
        $fullPath = Storage::disk('public')->path($zipPath);
        $dir = self::getSitePathFromZip($fullPath);

        if (! is_dir($dir)) {
            return;
        }

        // Source: https://stackoverflow.com/a/3349792
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator(
            $it,
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

    /**
     * @param  string  $path
     * @return bool|array
     */
    private static function extractSite(string $zipPath, bool $allowUnsafe = false)
    {
        $fullPath = Storage::disk('public')->path($zipPath);
        $archive = new ZipArchive;
        $result = $archive->open($fullPath);
        $filter = null;

        if ($result !== true) {
            return false;
        }

        if (! $allowUnsafe) {
            $filter = [];

            for ($i = 0; $i < $archive->numFiles; $i++) {
                $file = $archive->statIndex($i);

                // $mimeType =  mime_content_type('zip://' . $archive->filename . '#' . $file['name']); // unreliable
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION); // unsafe, but will have to do for now

                if (! in_array(strtolower($ext), self::EXT_ALLOWLIST)) {
                    continue;
                }

                $filter[] = $file['name'];
            }
        }

        $extractionPath = self::getSitePathFromZip($fullPath);
        $archive->extractTo($extractionPath, $filter);
        $archive->close();

        return $filter;
    }

    /**
     * @return Response
     */
    public function store(SiteStoreRequest $request)
    {
        if (! Gate::allows('create', Site::class)) {
            abort(403);
        }

        $validated = $request->validated();
        $succesfullyExtracted = [];

        if ($request->hasFile('path_nl')) {
            $validated['path_nl'] = $request->file('path_nl')->store('', 'public');

            if (($extracted = self::extractSite($validated['path_nl'], $request->boolean('allow_unsafe'))) !== false) {
                array_push($succesfullyExtracted, $extracted);
            }
        }

        if ($request->hasFile('path_en')) {
            $validated['path_en'] = $request->file('path_en')->store('', 'public');

            if (($extracted = self::extractSite($validated['path_en'], $request->boolean('allow_unsafe'))) !== false) {
                array_push($succesfullyExtracted, $extracted);
            }
        }

        $request->user()->sites()->create($validated);

        return redirect()
            ->route('sites.index')
            ->withSuccess(__('crud.common.created'))
            ->withDebug($succesfullyExtracted);
    }

    /**
     * @return Response
     */
    public function random(Request $request)
    {
        $site = Site::inRandomOrder()->first();

        if ($site === null) {
            return view('errors.no-sites');
        }

        return $this->show($request, $site);
    }

    /**
     * @return Response
     */
    public function randomEnglish(Request $request)
    {
        $site = Site::whereNotNull('path_en')->inRandomOrder()->first();

        if ($site === null) {
            return view('errors.no-sites', ['english' => true]);
        }

        return $this->show($request, $site)
            ->withEnglishLanguage(true);
    }

    /**
     * @return Response
     */
    public function show(Request $request, Site $site)
    {
        if (! Gate::allows('view', $site)) {
            abort(403);
        }

        return view('app.sites.show', compact('site'));
    }

    /**
     * @return Response
     */
    public function showEnglish(Request $request, Site $site)
    {
        if (! Gate::allows('view', $site)) {
            abort(403);
        }

        if ($site->path_en === null) {
            abort(404);
        }

        return view('app.sites.show', [
            'site' => $site,
            'englishLanguage' => true,
        ]);
    }

    /**
     * @return Response
     */
    public function edit(Request $request, Site $site)
    {
        if (! Gate::allows('update', $site)) {
            abort(403);
        }

        return view('app.sites.edit', compact('site'));
    }

    /**
     * @return Response
     */
    public function update(SiteUpdateRequest $request, Site $site)
    {
        if (! Gate::allows('update', $site)) {
            abort(403);
        }

        $validated = $request->validated();
        $succesfullyExtracted = [];

        if ($request->hasFile('path_nl')) {
            if ($site->path_nl) {
                Storage::disk('public')->delete($site->path_nl);
                self::removeSite($site->path_nl);
            }

            $validated['path_nl'] = $request->file('path_nl')->store('', 'public');

            if (($extracted = self::extractSite($validated['path_nl'], $request->boolean('allow_unsafe'))) !== false) {
                array_push($succesfullyExtracted, $extracted);
            }
        }

        if ($request->hasFile('path_en')) {
            if ($site->path_en) {
                Storage::disk('public')->delete($site->path_en);
                self::removeSite($site->path_en);
            }

            $validated['path_en'] = $request->file('path_en')->store('', 'public');

            if (($extracted = self::extractSite($validated['path_en'], $request->boolean('allow_unsafe'))) !== false) {
                array_push($succesfullyExtracted, $extracted);
            }
        }

        $site->update($validated);

        return redirect()
            ->route('sites.index', $site)
            ->withSuccess(__('crud.common.saved'))
            ->withDebug($succesfullyExtracted);
    }

    /**
     * @return Response
     */
    public function destroy(Request $request, Site $site)
    {
        if (! Gate::allows('delete', $site)) {
            abort(403);
        }

        if ($site->path_nl) {
            Storage::disk('public')->delete($site->path_nl);
            self::removeSite($site->path_nl);
        }

        if ($site->path_en) {
            Storage::disk('public')->delete($site->path_en);
            self::removeSite($site->path_en);
        }

        $site->delete();

        return redirect()
            ->route('sites.index')
            ->withSuccess(__('crud.common.removed'));
    }

    public function testShow(Request $request)
    {
        return view('app.sites.test');
    }

    public function testSubmit(Request $request)
    {
        $request->validate([
            'path' => ['required', 'file', 'mimes:zip'],
        ]);

        // If a site is already being tested, remove that immediately
        if (session()->has('path')) {
            $path = session()->pull('path');
            $sitePath = session()->pull('sitePath');

            RemoveTestSite::dispatch($path, $sitePath);
        }

        $path = $request->file('path')->store('temp', 'public');
        $sitePath = SiteController::getSitePathFromZip($path);
        $extracted = self::extractSite($path);

        if ($extracted === false) {
            Storage::disk('public')->delete($path);

            throw ValidationException::withMessages([
                'path' => __('validation.mimes', ['attribute' => 'path', 'values' => 'zip']),
            ]);
        }

        // Add app.sites.partials.site-tests to every html/php/etc file that was extracted
        foreach ($extracted as $file) {
            if (
                ! Str::endsWith($file, '.html')
                && ! Str::endsWith($file, '.php')
                && ! Str::endsWith($file, '.htm')
                && ! Str::endsWith($file, '.xhtml')
                && ! Str::endsWith($file, '.shtml')
            ) {
                continue;
            }

            $file = Storage::disk('public')->path($sitePath.$file);
            $contents = file_get_contents($file);
            $contents = str_replace('</body>', view('app.sites.partials.site-tests', compact('extracted'))->render().'</body>', $contents);
            file_put_contents($file, $contents);
        }

        // Remove the test site after a couple minutes
        RemoveTestSite::dispatch($path, $sitePath)
            ->delay(now()->addMinutes(config('app.site_testing.remove_after')));

        session()->put('path', $path);
        session()->put('sitePath', $sitePath);

        return redirect()->route('sites.test.submitted');
    }

    public function testSubmitted(Request $request)
    {
        $sitePath = session('sitePath');

        if ($sitePath === null || ! Storage::disk('public')->exists($sitePath)) {
            abort(404, __('crud.studenten_info_sites.test_submitted_expired'));
        }

        return view('app.sites.test-show', [
            'sitePath' => '/storage/'.$sitePath,
        ]);
    }
}
