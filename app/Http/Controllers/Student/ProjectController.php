<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Show the project proposal form.
     */
    public function create()
    {
        $student = Auth::user()->student;

        // A student can only have one active project
        $existingProject = Project::where('student_id', $student->id)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->first();

        if ($existingProject) {
            return redirect()->route('student.projects.show', $existingProject)
                ->with('info', 'You already have an active project.');
        }

        return view('student.projects.create');
    }

    /**
     * Store a newly submitted project proposal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'proposal' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $student = Auth::user()->student;

        // Store the uploaded file
        $file     = $request->file('proposal');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs(
            'proposals/' . $student->id,
            time() . '_' . $fileName,
            'local'
        );

        $project = Project::create([
            'student_id'         => $student->id,
            'title'              => $request->title,
            'proposal_file_path' => $filePath,
            'proposal_file_name' => $fileName,
            'proposal_mime_type' => $file->getMimeType(),
            'status'             => 'submitted',
            'submitted_at'       => now(),
        ]);

        return redirect()->route('student.projects.show', $project)
            ->with('success', 'Project proposal submitted successfully!');
    }

    /**
     * Show a single project and its milestones.
     */
    public function show(Project $project)
    {
        $student = Auth::user()->student;

        abort_if($project->student_id !== $student->id, 403);

        $milestones = $project->milestones;

        return view('student.projects.show', compact('project', 'milestones'));
    }
}