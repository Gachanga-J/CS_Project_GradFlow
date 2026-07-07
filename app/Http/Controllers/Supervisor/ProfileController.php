<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\ResearchTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $supervisor = Auth::user()->supervisor->load('tags');
        $allTags    = ResearchTag::orderBy('name')->get();

        return view('supervisor.profile.edit', compact('supervisor', 'allTags'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'staff_number'        => ['nullable', 'string', 'max:30'],
            'max_student_capacity'=> ['required', 'integer', 'between:1,255'],
            'tags'                => ['nullable', 'array'],
            'tags.*'              => ['integer', 'exists:research_tags,id'],
        ]);

        $supervisor = Auth::user()->supervisor;

        $supervisor->update([
            'staff_number'         => $request->staff_number,
            'max_student_capacity' => $request->max_student_capacity,
        ]);

        $supervisor->tags()->sync($request->tags ?? []);

        return back()->with('success', 'Profile updated successfully.');
    }
}
