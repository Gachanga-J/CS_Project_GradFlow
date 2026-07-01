<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ResearchTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function create()
    {
        $student = Auth::user()->student;

        // Redirect to existing project if one exists
        $existing = Project::where('student_id', $student->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return redirect()->route('student.projects.show', $existing)
                ->with('info', 'You already have an active project.');
        }

        $tags = ResearchTag::orderBy('name')->get();

        return view('student.projects.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;

        // Prevent duplicate active projects
        $existing = Project::where('student_id', $student->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return redirect()->route('student.projects.show', $existing)
                ->with('info', 'You already have an active project.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'tags'  => ['nullable', 'array'],
            'tags.*'=> ['integer', 'exists:research_tags,id'],
        ]);

        $project = Project::create([
            'student_id'    => $student->id,
            'supervisor_id' => null,
            'title'         => $request->title,
            'status'        => 'active',
        ]);

        $project->tags()->sync($request->tags ?? []);

        return redirect()->route('student.projects.show', $project)
            ->with('success', 'Project submitted! Your coordinator will assign a supervisor.');
    }

    public function show(Project $project)
    {
        $student = Auth::user()->student;

        abort_if($project->student_id !== $student->id, 403);

        $project->load(['tags', 'supervisor.user', 'supervisor.tags']);

        return view('student.projects.show', compact('project'));
    }
}
