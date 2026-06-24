<?php

namespace App\Http\Controllers\DepartmentCoordinator;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\MilestoneSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function index()
    {
        $milestones = Milestone::with([
            'submissions' => function ($query) {
                $query->where('is_latest', true)->with('student.user');
            }
        ])
        ->where('department_id', Auth::user()->department_id)
        ->orderBy('sequence_order')
        ->get();

        return view('department-coordinator.submissions.index', compact('milestones'));
    }

    public function download(MilestoneSubmission $submission)
    {
        // Ensure the submission belongs to this coordinator's department
        abort_if(
            $submission->milestone->department_id !== Auth::user()->department_id,
            403
        );

        abort_if(!$submission->file_path, 404);

        $fullPath = storage_path('app/private/' . $submission->file_path);

        abort_if(!file_exists($fullPath), 404, 'File not found.');

        return response()->download($fullPath, $submission->file_name);
    }

    public function grade(Request $request, MilestoneSubmission $submission)
    {
        // Ensure the submission belongs to this coordinator's department
        abort_if(
            $submission->milestone->department_id !== Auth::user()->department_id,
            403
        );

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
