<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\MilestoneSubmission;
use App\Models\Milestone;
use App\Notifications\SubmissionReviewed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @method \Illuminate\Notifications\DatabaseNotificationCollection notifications()
 * @method \Illuminate\Notifications\DatabaseNotificationCollection unreadNotifications()
 */
#[Fillable(['first_name', 'last_name', 'email', 'password', 'role', 'department_id', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    private function supervisorId(): int
    {
        return Auth::user()->supervisor->id;
    }

    /**
     * The department this user belongs to (nullable for system administrators).
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
        return match ($this->role) {
            'student' => 'dashboard.student',
            'supervisor' => 'dashboard.supervisor',
            'department_coordinator' => 'dashboard.department-coordinator',
            'system_administrator' => 'dashboard.system-administrator',
            default => 'login',
        };
    }

        $valid = $submission->student->projects()
            ->where('supervisor_id', $supervisorId)
            ->exists();

    /**
     * Check if the user is a department coordinator.
     */
    public function isDepartmentCoordinator(): bool
    {
        return $this->role === 'department_coordinator';
    }

    /**
     * Check if the user is a system administrator.
     */
    public function isSystemAdministrator(): bool
    {
        return $this->role === 'system_administrator';
    }
}
