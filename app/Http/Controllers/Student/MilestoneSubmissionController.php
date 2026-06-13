<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\MilestoneSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneSubmissionController extends Controller
{
    /**
     * Show a single milestone and the student's submission for it.
     */
    public function show(Milestone $milestone)
    {
        $student = Auth::user()->student;

        $submission = MilestoneSubmission::where('milestone_id', $milestone->id)
            ->where('student_id', $student->id)
            ->where('is_latest', true)
            ->first();

        return view('student.milestones.show', compact('milestone', 'submission'));
    }

    /**
     * Upload a submission for a milestone.
     */
    public function store(Request $request, Milestone $milestone)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $student = Auth::user()->student;

        abort_if($milestone->status === 'closed', 403, 'This milestone is closed.');

        // Get current version number
        $latestVersion = MilestoneSubmission::where('milestone_id', $milestone->id)
            ->where('student_id', $student->id)
            ->max('version_number') ?? 0;

        // Mark previous submissions as not latest
        MilestoneSubmission::where('milestone_id', $milestone->id)
            ->where('student_id', $student->id)
            ->update(['is_latest' => false]);

        // Store the file
        $file     = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs(
            'milestone_submissions/' . $milestone->id . '/' . $student->id,
            time() . '_' . $fileName,
            'local'
        );

        MilestoneSubmission::create([
            'milestone_id'  => $milestone->id,
            'student_id'    => $student->id,
            'file_path'     => $filePath,
            'file_name'     => $fileName,
            'file_size_bytes' => $file->getSize(),
            'mime_type'     => $file->getMimeType(),
            'version_number' => $latestVersion + 1,
            'is_latest'     => true,
            'status'        => 'submitted',
            'submitted_at'  => now(),
        ]);

        return redirect()->route('student.milestones.show', $milestone)
            ->with('success', 'Submission uploaded successfully!');
    }
}