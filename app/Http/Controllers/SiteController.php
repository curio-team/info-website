<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Requests\SiteStoreRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\SiteUpdateRequest;

class SiteController extends Controller
{
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
     * @param \App\Http\Requests\SiteStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SiteStoreRequest $request)
    {
        $this->authorize('create', Site::class);

        $validated = $request->validated();
        if ($request->hasFile('path_nl')) {
            $validated['path_nl'] = $request->file('path_nl')->store('public');
        }

        if ($request->hasFile('path_en')) {
            $validated['path_en'] = $request->file('path_en')->store('public');
        }

        $site = Site::create($validated);

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

        return view('app.sites.random', compact('site'));
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
            }

            $validated['path_nl'] = $request->file('path_nl')->store('public');
        }

        if ($request->hasFile('path_en')) {
            if ($site->path_en) {
                Storage::delete($site->path_en);
            }

            $validated['path_en'] = $request->file('path_en')->store('public');
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
        }

        if ($site->path_en) {
            Storage::delete($site->path_en);
        }

        $site->delete();

        return redirect()
            ->route('sites.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
