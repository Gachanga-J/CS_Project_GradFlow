<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\MilestoneSubmission;
use App\Models\Milestone;
use App\Notifications\SubmissionReviewed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    private function supervisorId(): int
    {
        return Auth::user()->supervisor->id;
    }

    /**
     * List all latest submissions for students assigned to this supervisor,
     * grouped by project then milestone.
     */
    public function index()
    {
        $supervisor = Auth::user()->supervisor->load(['projects.student.user', 'projects.student.milestoneSubmissions' => function ($q) {
            $q->where('is_latest', true)->with('milestone');
        }]);

        return view('supervisor.submissions.index', compact('supervisor'));
    }

    /**
     * Approve a submission. Only allowed when:
     * - submission belongs to a student supervised by this supervisor
     * - submission is in 'submitted' status
     */
    public function approve(Request $request, MilestoneSubmission $submission)
    {
        $this->authorizeSubmission($submission);

        abort_if($submission->status !== 'submitted', 422, 'Only pending submissions can be approved.');

        $request->validate([
            'supervisor_feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $submission->update([
            'status'              => 'supervisor_approved',
            'supervisor_feedback' => $request->supervisor_feedback,
        ]);

        // Notify student
        $submission->student->user->notify(new SubmissionReviewed($submission, 'approved'));

        return back()->with('success', 'Submission approved.');
    }

    /**
     * Reject a submission.
     */
    public function reject(Request $request, MilestoneSubmission $submission)
    {
        $this->authorizeSubmission($submission);

        abort_if($submission->status !== 'submitted', 422, 'Only pending submissions can be rejected.');

        $request->validate([
            'supervisor_feedback' => ['required', 'string', 'max:1000'],
        ]);

        $submission->update([
            'status'              => 'supervisor_rejected',
            'supervisor_feedback' => $request->supervisor_feedback,
        ]);

        // Notify student
        $submission->student->user->notify(new SubmissionReviewed($submission, 'rejected'));

        return back()->with('success', 'Submission rejected.');
    }

    /**
     * Download the submission file.
     */
    public function download(MilestoneSubmission $submission)
    {
        $this->authorizeSubmission($submission);

        $fullPath = storage_path('app/private/' . $submission->file_path);
        abort_if(!file_exists($fullPath), 404, 'File not found.');

        return response()->download($fullPath, $submission->file_name);
    }

    /**
     * Ensure the submission belongs to a student assigned to this supervisor.
     */
    private function authorizeSubmission(MilestoneSubmission $submission): void
    {
        $supervisorId = $this->supervisorId();

        $valid = $submission->student->projects()
            ->where('supervisor_id', $supervisorId)
            ->exists();

        abort_unless($valid, 403, 'This submission does not belong to one of your students.');
    }
}
