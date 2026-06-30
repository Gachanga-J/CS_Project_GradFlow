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

        $projects = $supervisor->projects->map(function ($project) {
            $submissions = $project->student->milestoneSubmissions;

            $departmentId = $project->student->user->department_id;
            $milestones   = Milestone::where('department_id', $departmentId)
                ->where('status', 'open')
                ->orderBy('sequence_order')
                ->get();

            $nextMilestone = $milestones->first(function ($milestone) use ($submissions) {
                $sub = $submissions->firstWhere('milestone_id', $milestone->id);
                return !$sub || !in_array($sub->status, ['supervisor_approved', 'graded']);
            });

            $project->next_milestone = $nextMilestone;

            $completedCount = $milestones->filter(function ($milestone) use ($submissions) {
                $sub = $submissions->firstWhere('milestone_id', $milestone->id);
                return $sub && in_array($sub->status, ['supervisor_approved', 'graded']);
            })->count();

            $project->milestones_total     = $milestones->count();
            $project->milestones_completed = $completedCount;

            return $project;
        });

        return view('supervisor.students.index', compact('supervisor', 'projects'));
    }

    /**
     * Show the supervisor dashboard with pending-review stats and a
     * per-department, per-milestone submission overview.
     */
    public function dashboard()
    {
        $supervisor = Auth::user()->supervisor;

        if (! $supervisor) {
            return view('dashboard.supervisor', [
                'pendingCount'        => 0,
                'activeStudents'      => 0,
                'departmentMilestones' => collect(),
            ]);
        }

        $supervisor->load([
            'projects.student.user.department',
            'projects.student.milestoneSubmissions' => function ($q) {
                $q->where('is_latest', true);
            },
        ]);

        $studentIds = $supervisor->projects->pluck('student_id');

        $pendingCount = MilestoneSubmission::where('status', 'submitted')
            ->where('is_latest', true)
            ->whereIn('student_id', $studentIds)
            ->count();

        $activeStudents = $supervisor->projects->where('status', 'active')->count();

        // Group this supervisor's projects by the student's department
        $projectsByDepartment = $supervisor->projects->groupBy(function ($project) {
            return $project->student->user->department_id;
        });

        $departmentMilestones = $projectsByDepartment->map(function ($projectsInDept) {
            $firstStudent  = $projectsInDept->first()->student;
            $department    = $firstStudent->user->department;
            $departmentId  = $firstStudent->user->department_id;

            $totalStudentsInDept = $projectsInDept->pluck('student_id')->unique()->count();

            $milestones = Milestone::where('department_id', $departmentId)
                ->where('status', 'open')
                ->orderBy('sequence_order')
                ->get();

            $allSubmissions = $projectsInDept->flatMap(function ($project) {
                return $project->student->milestoneSubmissions;
            });

            $milestoneStats = $milestones->map(function ($milestone) use ($allSubmissions, $totalStudentsInDept) {
                $submittedCount = $allSubmissions->where('milestone_id', $milestone->id)->count();

                return [
                    'milestone'       => $milestone,
                    'submitted_count' => $submittedCount,
                    'not_submitted'   => max(0, $totalStudentsInDept - $submittedCount),
                    'total_students'  => $totalStudentsInDept,
                    'percent'         => $totalStudentsInDept > 0 ? round(($submittedCount / $totalStudentsInDept) * 100) : 0,
                ];
            });

            return [
                'department' => $department,
                'stats'      => $milestoneStats,
            ];
        })->values();

        return view('dashboard.supervisor', compact('pendingCount', 'activeStudents', 'departmentMilestones'));
    }
}
