<?php

namespace App\Http\Controllers\SystemAdministrator;

use App\Http\Controllers\Controller;
use App\Models\ResearchTag;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ResearchTagController extends Controller
{
    public function index(): View
    {
        $tags = ResearchTag::withCount(['supervisors', 'projects'])
            ->orderBy('name')
            ->get();

        return view('system-administrator.tags.index', compact('tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:research_tags,name'],
        ]);

        ResearchTag::create(['name' => $request->name]);

        return back()->with('success', "Tag \"{$request->name}\" created.");
    }

    public function update(Request $request, ResearchTag $tag): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:research_tags,name,' . $tag->id],
        ]);

        $tag->update(['name' => $request->name]);

        return back()->with('success', 'Tag updated.');
    }

    public function destroy(ResearchTag $tag): RedirectResponse
    {
        $tag->delete();
        return back()->with('success', 'Tag deleted.');
    }
}
