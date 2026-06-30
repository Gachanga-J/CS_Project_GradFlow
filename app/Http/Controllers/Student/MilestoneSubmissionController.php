<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\MilestoneSubmission;
use App\Models\Supervisor;
use App\Models\Project;
use App\Notifications\NewSubmissionReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneSubmissionController extends Controller
{
    public function show(Milestone $milestone)
    {
        $user = Auth::user();
        abort_if($milestone->department_id !== $user->department_id, 403);

        $student = $user->student;

        // State-gate check: ensure all prior milestones are supervisor_approved
        $this->enforceStateGate($milestone, $student->id);

        $submission = MilestoneSubmission::where('milestone_id', $milestone->id)
            ->where('student_id', $student->id)
            ->where('is_latest', true)
            ->first();

        // All versions for history
        $submissionHistory = MilestoneSubmission::where('milestone_id', $milestone->id)
            ->where('student_id', $student->id)
            ->orderBy('version_number', 'desc')
            ->get();

        // Is the student currently submitting/resubmitting past the deadline?
        $isLate = $milestone->deadline
            && now()->greaterThan($milestone->deadline->endOfDay())
            && $milestone->status === 'open';

        return view('student.milestones.show', compact('milestone', 'submission', 'submissionHistory', 'isLate'));
    }

    public function store(Request $request, Milestone $milestone)
    {
        $user = Auth::user();
        abort_if($milestone->department_id !== $user->department_id, 403);
        abort_if($milestone->status === 'closed', 403, 'This milestone is closed.');

        $student = $user->student;

        // State-gate: block submission if prior milestone not yet supervisor_approved
        $this->enforceStateGate($milestone, $student->id);

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

        // Notify the assigned supervisor, if this student has an active project with one
        $project = $student->projects()->whereNotNull('supervisor_id')->first();
        if ($project && $project->supervisor) {
            $project->supervisor->user->notify(new NewSubmissionReceived($submission));
        }

        return redirect()->route('student.milestones.show', $milestone)
            ->with($isLate ? 'warning' : 'success', $isLate
                ? 'Submission uploaded, but it was after the deadline and has been marked late.'
                : 'Submission uploaded successfully!');
    }

    /**
     * Show ranked supervisor matches for the student's active projects.
     */
    public function supervisorMatches()
    {
        $student = Auth::user()->student;

        $projects = Project::with(['tags', 'supervisor.user', 'supervisor.tags'])
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->get()
            ->map(function ($project) {
                // Rank all supervisors by compatibility (only if no supervisor yet)
                if (!$project->supervisor_id) {
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
     * State-gate: abort if any previous milestone (lower sequence_order)
     * does not have a supervisor_approved submission for this student.
     */
    private function enforceStateGate(Milestone $milestone, int $studentId): void
    {
        $priorMilestones = Milestone::where('department_id', $milestone->department_id)
            ->where('sequence_order', '<', $milestone->sequence_order)
            ->orderBy('sequence_order')
            ->get();

        foreach ($priorMilestones as $prior) {
            $approved = MilestoneSubmission::where('milestone_id', $prior->id)
                ->where('student_id', $studentId)
                ->where('is_latest', true)
                ->whereIn('status', ['supervisor_approved', 'graded'])
                ->exists();

            if (!$approved) {
                abort(403, "You must have milestone \"{$prior->title}\" approved by your supervisor before submitting this one.");
            }
        }
    }
}
