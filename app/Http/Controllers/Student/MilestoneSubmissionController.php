<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\MilestoneSubmission;
use App\Models\Project;
use App\Models\Supervisor;
use App\Notifications\NewSubmissionReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneSubmissionController extends Controller
{
    /**
     * Return the three scoping constraints for the authenticated student.
     */
    private function milestoneScope(): array
    {
        $user    = Auth::user();
        $student = $user->student;

        return [
            'department_id' => $user->department_id,
            'intake_year'   => $student->intake_year,
            'year_of_study' => $student->year_of_study,
        ];
    }

    public function show(Milestone $milestone)
    {
        $user    = Auth::user();
        $student = $user->student;
        $scope   = $this->milestoneScope();

        // Confirm this milestone belongs to this student's exact scope
        abort_if(
            $milestone->department_id !== $scope['department_id'] ||
            $milestone->intake_year   !== $scope['intake_year']   ||
            $milestone->year_of_study !== $scope['year_of_study'],
            403,
            'You do not have access to this milestone.'
        );

        // Auto-mark any unread notifications about this milestone as read
        $user->unreadNotifications()
            ->whereJsonContains('data->milestone_id', $milestone->id)
            ->get()
            ->each(fn($n) => $n->markAsRead());

        // State-gate check
        $this->enforceStateGate($milestone, $student->id, $scope);

        $submission = MilestoneSubmission::where('milestone_id', $milestone->id)
            ->where('student_id', $student->id)
            ->where('is_latest', true)
            ->first();

        $submissionHistory = MilestoneSubmission::where('milestone_id', $milestone->id)
            ->where('student_id', $student->id)
            ->orderBy('version_number', 'desc')
            ->get();

        $isLate = $milestone->deadline
            && now()->greaterThan($milestone->deadline->endOfDay())
            && $milestone->status === 'open';

        return view('student.milestones.show', compact(
            'milestone', 'submission', 'submissionHistory', 'isLate'
        ));
    }

    public function store(Request $request, Milestone $milestone)
    {
        $user    = Auth::user();
        $student = $user->student;
        $scope   = $this->milestoneScope();

        // Scope check
        abort_if(
            $milestone->department_id !== $scope['department_id'] ||
            $milestone->intake_year   !== $scope['intake_year']   ||
            $milestone->year_of_study !== $scope['year_of_study'],
            403,
            'You do not have access to this milestone.'
        );

        abort_if($milestone->status === 'closed', 403, 'This milestone is closed.');

        // Supervisor assignment check
        $project = $student->projects()->whereNotNull('supervisor_id')->first();
        abort_if(! $project, 403, 'You cannot submit until a supervisor has been assigned to your project.');

        // State-gate check
        $this->enforceStateGate($milestone, $student->id, $scope);

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $isLate = $milestone->deadline
            && now()->greaterThan($milestone->deadline->endOfDay());

        $latestVersion = MilestoneSubmission::where('milestone_id', $milestone->id)
            ->where('student_id', $student->id)
            ->max('version_number') ?? 0;

        MilestoneSubmission::where('milestone_id', $milestone->id)
            ->where('student_id', $student->id)
            ->update(['is_latest' => false]);

        $file     = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs(
            'milestone_submissions/' . $milestone->id . '/' . $student->id,
            time() . '_' . $fileName,
            'local'
        );

        $submission = MilestoneSubmission::create([
            'milestone_id'    => $milestone->id,
            'student_id'      => $student->id,
            'file_path'       => $filePath,
            'file_name'       => $fileName,
            'file_size_bytes' => $file->getSize(),
            'mime_type'       => $file->getMimeType(),
            'version_number'  => $latestVersion + 1,
            'is_latest'       => true,
            'status'          => 'submitted',
            'submitted_at'    => now(),
            'submitted_late'  => $isLate,
        ]);

        // Notify assigned supervisor
        if ($project->supervisor) {
            $project->supervisor->user->notify(new NewSubmissionReceived($submission));
        }

        return redirect()->route('student.milestones.show', $milestone)
            ->with(
                $isLate ? 'warning' : 'success',
                $isLate
                    ? 'Submission uploaded, but it was after the deadline and has been marked late.'
                    : 'Submission uploaded successfully!'
            );
    }

    /**
     * Show ranked supervisor matches for the student's active project.
     */
    public function supervisorMatches()
    {
        $student = Auth::user()->student;

        $projects = Project::with(['tags', 'supervisor.user', 'supervisor.tags'])
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->get()
            ->map(function ($project) {
                if (! $project->supervisor_id) {
                    $supervisors = Supervisor::with(['user', 'tags'])
                        ->whereHas('user', fn($q) => $q->where('is_active', true))
                        ->get()
                        ->map(function ($sup) use ($project) {
                            $sup->compatibility = $project->compatibilityScore($sup);
                            return $sup;
                        })
                        ->sortByDesc('compatibility')
                        ->take(5)
                        ->values();

                    $project->ranked_supervisors = $supervisors;
                } else {
                    $project->ranked_supervisors = collect();
                }

                return $project;
            });

        return view('student.supervisor-matches', compact('projects'));
    }

    /**
     * State-gate: block submission if any prior milestone (lower sequence_order)
     * in the same scope does not have a supervisor_approved or graded submission.
     */
    private function enforceStateGate(Milestone $milestone, int $studentId, array $scope): void
    {
        $priorMilestones = Milestone::where('department_id', $scope['department_id'])
            ->where('intake_year',   $scope['intake_year'])
            ->where('year_of_study', $scope['year_of_study'])
            ->where('sequence_order', '<', $milestone->sequence_order)
            ->orderBy('sequence_order')
            ->get();

        foreach ($priorMilestones as $prior) {
            $approved = MilestoneSubmission::where('milestone_id', $prior->id)
                ->where('student_id', $studentId)
                ->where('is_latest', true)
                ->whereIn('status', ['supervisor_approved', 'graded'])
                ->exists();

            if (! $approved) {
                abort(403, "You must have milestone \"{$prior->title}\" approved by your supervisor before submitting this one.");
            }
        }
    }
}
