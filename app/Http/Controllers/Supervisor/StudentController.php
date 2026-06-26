<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\MilestoneSubmission;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Show all students assigned to this supervisor with project/milestone progress.
     */
    public function index()
    {
        $supervisor = Auth::user()->supervisor->load([
            'projects.student.user',
            'projects.tags',
            'projects.student.milestoneSubmissions' => function ($q) {
                $q->where('is_latest', true)->with('milestone');
            },
        ]);

        // For each project, determine next pending milestone
        $projects = $supervisor->projects->map(function ($project) {
            $submissions = $project->student->milestoneSubmissions;

            // Load all milestones for this student's department
            $departmentId = $project->student->user->department_id;
            $milestones   = Milestone::where('department_id', $departmentId)
                ->where('status', 'open')
                ->orderBy('sequence_order')
                ->get();

            // Find first milestone not yet supervisor_approved or graded
            $nextMilestone = $milestones->first(function ($milestone) use ($submissions) {
                $sub = $submissions->firstWhere('milestone_id', $milestone->id);
                return !$sub || !in_array($sub->status, ['supervisor_approved', 'graded']);
            });

            $project->next_milestone = $nextMilestone;
            return $project;
        });

        return view('supervisor.students.index', compact('supervisor', 'projects'));
    }
}
