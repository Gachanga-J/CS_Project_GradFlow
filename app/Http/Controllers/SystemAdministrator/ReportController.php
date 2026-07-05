<?php

namespace App\Http\Controllers\SystemAdministrator;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Milestone;
use App\Models\MilestoneSubmission;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $departments  = Department::orderBy('name')->get();
        $selectedDept = $request->input('department_id', $departments->first()?->id);

        // Always initialize with empty collections
        $cohortYears  = collect();
        $yearsOfStudy = collect();
        $selectedYear = null;
        $selectedYos  = null;

        if ($selectedDept) {
            $cohortYears = Student::whereHas('user', fn($q) => $q->where('department_id', $selectedDept))
                ->whereNotNull('intake_year')
                ->orderByDesc('intake_year')
                ->distinct()
                ->pluck('intake_year');

            $yearsOfStudy = Student::whereHas('user', fn($q) => $q->where('department_id', $selectedDept))
                ->whereNotNull('year_of_study')
                ->orderBy('year_of_study')
                ->distinct()
                ->pluck('year_of_study');

            $selectedYear = $request->input('intake_year', $cohortYears->first());
            $selectedYos  = $request->input('year_of_study', $yearsOfStudy->first());
        }

        // Department-level overview stats
        $deptStats = $departments->map(function ($dept) {
            return [
                'name'              => $dept->name,
                'total_students'    => User::where('role', 'student')
                    ->where('department_id', $dept->id)->count(),
                'active_students'   => User::where('role', 'student')
                    ->where('department_id', $dept->id)->where('is_active', true)->count(),
                'supervisors'       => User::where('role', 'supervisor')
                    ->whereHas('supervisor.projects.student.user',
                        fn($q) => $q->where('department_id', $dept->id))->count(),
                'total_milestones'  => Milestone::where('department_id', $dept->id)->count(),
                'total_submissions' => MilestoneSubmission::where('is_latest', true)
                    ->whereHas('student.user', fn($q) => $q->where('department_id', $dept->id))->count(),
            ];
        });

        // Cohort-scoped data
        $cohortStats     = collect();
        $milestoneStats  = collect();
        $supervisorStats = collect();
        $totalStudents   = 0;
        $totalMilestones = 0;
        $overallAverage  = 0;
        $lateCount       = 0;

        if ($selectedDept && $selectedYear && $selectedYos) {

            $students = Student::whereHas('user', fn($q) => $q->where('department_id', $selectedDept))
                ->where('intake_year', $selectedYear)
                ->where('year_of_study', $selectedYos)
                ->with([
                    'user',
                    'milestoneSubmissions' => fn($q) => $q->where('is_latest', true)->with('milestone'),
                    'projects.supervisor.user',
                ])
                ->get();

            $totalStudents = $students->count();

            $milestones = Milestone::where('department_id', $selectedDept)
                ->where('intake_year', $selectedYear)
                ->where('year_of_study', $selectedYos)
                ->orderBy('sequence_order')
                ->get();

            $totalMilestones = $milestones->count();

            $allSubmissions = $students->flatMap(fn($s) => $s->milestoneSubmissions);
            $lateCount      = $allSubmissions->where('submitted_late', true)->count();
            $graded         = $allSubmissions->where('status', 'graded')->whereNotNull('grade');
            $overallAverage = $graded->isNotEmpty() ? round($graded->avg('grade'), 1) : 0;

            $milestoneStats = $milestones->map(function ($milestone) use ($allSubmissions, $totalStudents) {
                $submitted = $allSubmissions->where('milestone_id', $milestone->id)->count();
                $awaitingGrading = $allSubmissions->where('milestone_id', $milestone->id)
                    ->where('status', 'supervisor_approved')->count();
                return [
                    'title'            => $milestone->title,
                    'sequence_order'   => $milestone->sequence_order,
                    'submitted'        => $submitted,
                    'awaiting_grading' => $awaitingGrading,
                    'completion_rate'  => $totalStudents > 0
                        ? round(($submitted / $totalStudents) * 100) : 0,
                ];
            });

            $cohortStats = $students->map(function ($student) use ($totalMilestones) {
                $submissions = $student->milestoneSubmissions;
                $graded      = $submissions->where('status', 'graded');
                $supervisor  = $student->projects->first()?->supervisor?->user?->full_name ?? '—';

                return [
                    'name'             => $student->user->full_name,
                    'reg_number'       => $student->reg_number,
                    'supervisor'       => $supervisor,
                    'submitted'        => $submissions->count(),
                    'awaiting_grading' => $submissions->where('status', 'supervisor_approved')->count(),
                    'graded'           => $graded->count(),
                    'late'             => $submissions->where('submitted_late', true)->count(),
                    'avg_grade'        => $graded->isNotEmpty()
                        ? round($graded->avg('grade'), 1) : null,
                    'completion_rate'  => $totalMilestones > 0
                        ? round(($submissions->count() / $totalMilestones) * 100) : 0,
                ];
            });

            $supervisorStats = Supervisor::whereHas('projects.student.user',
                    fn($q) => $q->where('department_id', $selectedDept))
                ->with(['user', 'projects' => fn($q) =>
                    $q->whereHas('student', fn($sq) =>
                        $sq->whereHas('user', fn($uq) => $uq->where('department_id', $selectedDept))
                           ->where('intake_year', $selectedYear)
                           ->where('year_of_study', $selectedYos)
                    )->with(['student.milestoneSubmissions' => fn($mq) =>
                        $mq->where('is_latest', true)
                    ])
                ])
                ->get()
                ->map(function ($supervisor) {
                    $submissions = $supervisor->projects->flatMap(
                        fn($p) => $p->student->milestoneSubmissions ?? collect()
                    );
                    $graded = $submissions->where('status', 'graded');
                    return [
                        'name'          => $supervisor->user->full_name,
                        'student_count' => $supervisor->projects->count(),
                        'reviewed'      => $submissions->whereIn('status', ['supervisor_approved', 'graded'])->count(),
                        'pending'       => $submissions->where('status', 'submitted')->count(),
                        'avg_grade'     => $graded->isNotEmpty()
                            ? round($graded->avg('grade'), 1) : null,
                    ];
                })
                ->filter(fn($s) => $s['student_count'] > 0)
                ->values();
        }

        return view('system-administrator.reports.index', compact(
            'departments', 'selectedDept', 'cohortYears', 'yearsOfStudy',
            'selectedYear', 'selectedYos', 'deptStats',
            'totalStudents', 'totalMilestones', 'overallAverage', 'lateCount',
            'milestoneStats', 'cohortStats', 'supervisorStats'
        ));
    }

    public function export(Request $request)
    {
        $deptId      = $request->input('department_id');
        $intakeYear  = $request->input('intake_year');
        $yearOfStudy = $request->input('year_of_study');

        $students = Student::whereHas('user', fn($q) => $q->where('department_id', $deptId))
            ->where('intake_year', $intakeYear)
            ->where('year_of_study', $yearOfStudy)
            ->with([
                'user',
                'milestoneSubmissions' => fn($q) => $q->where('is_latest', true),
                'projects.supervisor.user',
            ])
            ->get();

        $milestones = Milestone::where('department_id', $deptId)
            ->where('intake_year', $intakeYear)
            ->where('year_of_study', $yearOfStudy)
            ->orderBy('sequence_order')
            ->get();

        $totalMilestones = $milestones->count();

        $headers = [
            'Student Name', 'Reg Number', 'Supervisor',
            'Submitted', 'Awaiting Grading', 'Graded', 'Late', 'Avg Grade', 'Completion %',
        ];

        $rows = $students->map(function ($student) use ($totalMilestones) {
            $submissions = $student->milestoneSubmissions;
            $graded      = $submissions->where('status', 'graded');
            $supervisor  = $student->projects->first()?->supervisor?->user?->full_name ?? '—';

            return [
                $student->user->full_name,
                $student->reg_number,
                $supervisor,
                $submissions->count(),
                $submissions->where('status', 'supervisor_approved')->count(),
                $graded->count(),
                $submissions->where('submitted_late', true)->count(),
                $graded->isNotEmpty() ? round($graded->avg('grade'), 1) : '—',
                $totalMilestones > 0
                    ? round(($submissions->count() / $totalMilestones) * 100) . '%' : '0%',
            ];
        });

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        foreach ($rows as $row) fputcsv($handle, $row);
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        $dept     = Department::find($deptId);
        $filename = "sysadmin_report_{$dept?->name}_{$intakeYear}_year{$yearOfStudy}_"
            . now()->format('Ymd') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
