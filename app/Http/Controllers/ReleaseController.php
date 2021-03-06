<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReleaseFormRequest;
use App\Models\Release;
use App\Models\ReleaseChangelog;
use App\Models\ReleaseChangelogCategory;
use App\Models\ReleaseChangelogChange;
use Illuminate\Http\Request;

class ReleaseController extends Controller
{
    /**
     * @param ReleaseFormRequest $request
     * @param Release $release
     * @return mixed
     * @throws \Exception
     */
    public function store(ReleaseFormRequest $request, Release $release = null)
    {
        if ($new = ($release === null)) {
            $release = new Release();
            $changelog = new ReleaseChangelog();
        } else {
            $changelog = $release->changelog;
        }

        // Update the changelog
        $changelog->description = $request->get('changelog_description');
        $changelog->save();

        // Update changes
        $tickets = $request->get('tickets', []);
        $changes = $request->get('changes', []);
        $categories = $request->get('categories', []);

        // Delete existing changes
        $changelog->changes()->delete();
        // Unset the relation so it's reloaded
        $changelog->unsetRelation('changes');
        for ($i = 0; $i < count($tickets); $i++) {
            // Only filled in rows, but tickets may be null
            if (/*strlen($tickets[$i]) > 0 && */ (int)$categories[$i] !== -1 && strlen($categories[$i]) > 0 && strlen($changes[$i]) > 0) {
                // Add new changes
                $changelogChange = new ReleaseChangelogChange();
                $changelogChange->release_changelog_id = $changelog->id;
                $changelogChange->release_changelog_category_id = $categories[$i];
                $changelogChange->ticket_id = intval(str_replace('#', '', $tickets[$i]));
                $changelogChange->change = $changes[$i];
                $changelogChange->save();
            }
        }
        $changelog->load('changes');


        $release->version = $request->get('version');

        // Match the changelog to the release
        $release->release_changelog_id = $changelog->id;

        if ($release->save()) {
            $changelog->release_id = $release->id;
            $changelog->save();
        } // Something went wrong with saving
        else {
            abort(500, 'Unable to save release');
        }

        return $release;
    }

    /**
     * Show a page for creating a new release.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function new()
    {
        return view('admin.release.edit', [
            'headerTitle' => __('New release'),
            'categories'  => ReleaseChangelogCategory::all()
        ]);
    }

    /**
     * @param Request $request
     * @param Release $release
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, Release $release)
    {
        return view('admin.release.edit', [
            'model'       => $release,
            'headerTitle' => __('Edit release'),
            'categories'  => ReleaseChangelogCategory::all()
        ]);
    }

    /**
     * @param ReleaseFormRequest $request
     * @param Release $release
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function update(ReleaseFormRequest $request, Release $release)
    {
        // Store it and show the edit page again
        $release = $this->store($request, $release);

        // Message to the user
        \Session::flash('status', __('Release updated'));

        // Display the edit page
        return $this->edit($request, $release);
    }

    /**
     * @param ReleaseFormRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function savenew(ReleaseFormRequest $request)
    {
        // Store it and show the edit page
        $release = $this->store($request);

        // Message to the user
        \Session::flash('status', __('Release created'));

        return redirect()->route('admin.release.edit', ['release' => $release]);
    }

    /**
     * Handles the viewing of a collection of items in a table.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\
     */
    public function list()
    {
        return view('admin.release.list', ['models' => Release::all()]);
    }

    /**
     * @param Release $release
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view(Release $release)
    {
        return view('release.view', ['release' => $release]);
    }
}
