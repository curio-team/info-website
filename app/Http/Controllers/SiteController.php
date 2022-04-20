<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Requests\SiteStoreRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\SiteUpdateRequest;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class SiteController extends Controller
{
    // TODO: This simple means of checking files is unsafe. Teachers should double-check what they upload.
    public const EXTENSION_ALLOWLIST = [
        'html', 'htm', 'css', 'js', 'png', 'jpg', 'jpeg', 'gif'
    ];

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Site::class);

        $search = $request->get('search', '');

        $sites = Site::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.sites.index', compact('sites', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Site::class);

        return view('app.sites.create');
    }

    /**
     * @param string $zipPath
     * @return string
     */
    public static function getSitePathFromZip(string $zipPath)
    {
        return rtrim($zipPath, '.zip').'/';
    }

    /**
     * @param string $path
     * @return bool
     */
    private static function removeSite(string $zipPath)
    {
        $fullPath = Storage::path($zipPath);
        $dir = self::getSitePathFromZip($fullPath);

        // Source: https://stackoverflow.com/a/3349792
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
                    RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

    /**
     * @param string $path
     * @return bool
     */
    private static function extractSite(string $zipPath, bool $allowUnsafe = false)
    {
        $fullPath = Storage::path($zipPath);
        $archive = new ZipArchive;
        $result = $archive->open($fullPath);
        $filter = null;

        if(!$allowUnsafe){
            $filter = [];

            // TODO: This simple means of checking files is unsafe. Teachers should double-check what they upload.
            for($i = 0; $i < $archive->numFiles; $i++){
                $file = $archive->statIndex($i);

                if(!in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), self::EXTENSION_ALLOWLIST)){
                    continue;
                }

                $filter[] = $file['name'];
            }
        }

        if ($result !== TRUE)
            dd("Couldn't open archive file. TODO: Handle nicely instead of dying. ($result, $fullPath)");

        $archive->extractTo(self::getSitePathFromZip($fullPath), $filter);
        $archive->close();
    }

    /**
     * @param \App\Http\Requests\SiteStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SiteStoreRequest $request)
    {
        $this->authorize('create', Site::class);

        $validated = $request->validated();
        if ($request->hasFile('path_nl')) {
            $validated['path_nl'] = $request->file('path_nl')->store('public');
            self::extractSite($validated['path_nl'], $request->allow_unsafe);
        }

        if ($request->hasFile('path_en')) {
            $validated['path_en'] = $request->file('path_en')->store('public');
            self::extractSite($validated['path_en'], $request->allow_unsafe);
        }

        $site = $request->user()->sites()->create($validated);

        return redirect()
            ->route('sites.edit', $site)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function random(Request $request)
    {
        $site = Site::inRandomOrder()->first();

        return $this->show($request, $site);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Site $site
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Site $site)
    {
        $this->authorize('view', $site);

        return view('app.sites.show', compact('site'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Site $site
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Site $site)
    {
        $this->authorize('update', $site);

        return view('app.sites.edit', compact('site'));
    }

    /**
     * @param \App\Http\Requests\SiteUpdateRequest $request
     * @param \App\Models\Site $site
     * @return \Illuminate\Http\Response
     */
    public function update(SiteUpdateRequest $request, Site $site)
    {
        $this->authorize('update', $site);

        $validated = $request->validated();
        if ($request->hasFile('path_nl')) {
            if ($site->path_nl) {
                Storage::delete($site->path_nl);
                self::removeSite($site->path_nl);
            }

            $validated['path_nl'] = $request->file('path_nl')->store('public');
            self::extractSite($validated['path_nl'], $request->allow_unsafe);
        }

        if ($request->hasFile('path_en')) {
            if ($site->path_en) {
                Storage::delete($site->path_en);
                self::removeSite($site->path_en);
            }

            $validated['path_en'] = $request->file('path_en')->store('public');
            self::extractSite($validated['path_en'], $request->allow_unsafe);
        }

        $site->update($validated);

        return redirect()
            ->route('sites.edit', $site)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Site $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Site $site)
    {
        $this->authorize('delete', $site);

        if ($site->path_nl) {
            Storage::delete($site->path_nl);
            self::removeSite($site->path_nl);
        }

        if ($site->path_en) {
            Storage::delete($site->path_en);
            self::removeSite($site->path_en);
        }

        $site->delete();

        return redirect()
            ->route('sites.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
