<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\MilestoneSubmission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    /**
     * Show all milestone submissions across all students.
     */
    public function index()
    {
        $milestones = Milestone::with([
            'submissions' => function ($query) {
                $query->where('is_latest', true)->with('student.user');
            }
        ])->orderBy('sequence_order')->get();

        return view('admin.submissions.index', compact('milestones'));
    }

    /**
     * Download a submission file.
     */
    public function download(MilestoneSubmission $submission)
    {
        abort_if(!$submission->file_path, 404);

        return response()->download(
            storage_path('app/' . $submission->file_path),
            $submission->file_name
        );
    }

    /**
     * Grade a submission.
     */
    public function grade(Request $request, MilestoneSubmission $submission)
    {
        $request->validate([
            'grade'          => ['required', 'integer', 'min:0', 'max:100'],
            'admin_feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $submission->update([
            'grade'          => $request->grade,
            'admin_feedback' => $request->admin_feedback,
            'status'         => 'graded',
        ]);

        return back()->with('success', 'Submission graded successfully!');
    }
}