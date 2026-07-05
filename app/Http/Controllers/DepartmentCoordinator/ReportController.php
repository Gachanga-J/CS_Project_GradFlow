<?php

namespace App\Http\Controllers\DepartmentCoordinator;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\MilestoneSubmission;
use App\Models\Student;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private function departmentId(): int
    {
        return Auth::user()->department_id;
    }

    public function index(Request $request)
    {
        $deptId = $this->departmentId();

        // Available cohorts in this department
        $cohortYears = Student::whereHas('user', fn($q) => $q->where('department_id', $deptId))
            ->whereNotNull('intake_year')
            ->orderByDesc('intake_year')
            ->distinct()
            ->pluck('intake_year');

        // Available years of study in this department
        $yearsOfStudy = Student::whereHas('user', fn($q) => $q->where('department_id', $deptId))
            ->whereNotNull('year_of_study')
            ->orderBy('year_of_study')
            ->distinct()
            ->pluck('year_of_study');

        // If no cohorts exist yet, return safe empty defaults
        if ($cohortYears->isEmpty()) {
            return view('department-coordinator.reports.index', [
                'cohortYears'          => $cohortYears,
                'yearsOfStudy'         => $yearsOfStudy,
                'selectedYear'         => null,
                'selectedYearOfStudy'  => null,
                'totalStudents'        => 0,
                'totalMilestones'      => 0,
                'lateSubmissionsCount' => 0,
                'overallAverage'       => 0,
                'milestoneStats'       => collect(),
                'studentStats'         => collect(),
                'supervisorWorkload'   => collect(),
            ]);
        }

        $selectedYear        = $request->input('intake_year', $cohortYears->first());
        $selectedYearOfStudy = $request->input('year_of_study', $yearsOfStudy->first());

        // CSV export
        if ($request->input('export') === 'csv') {
            return $this->exportCsv($deptId, $selectedYear, $selectedYearOfStudy);
        }

        // Students scoped by department + intake year + year of study
        $students = Student::whereHas('user', fn($q) => $q->where('department_id', $deptId))
            ->where('intake_year', $selectedYear)
            ->where('year_of_study', $selectedYearOfStudy)
            ->with([
                'user',
                'milestoneSubmissions' => fn($q) => $q->where('is_latest', true)->with('milestone'),
                'projects.supervisor.user',
            ])
            ->get();

        $totalStudents = $students->count();

        // Milestones scoped by department + intake year + year of study
        $milestones = Milestone::where('department_id', $deptId)
            ->where('intake_year', $selectedYear)
            ->where('year_of_study', $selectedYearOfStudy)
            ->orderBy('sequence_order')
            ->get();

        $totalMilestones = $milestones->count();

        // All latest submissions for this cohort + year of study
        $allSubmissions = $students->flatMap(fn($s) => $s->milestoneSubmissions);

        $lateSubmissionsCount = $allSubmissions->where('submitted_late', true)->count();

        $gradedSubmissions = $allSubmissions->where('status', 'graded')->whereNotNull('grade');
        $overallAverage    = $gradedSubmissions->isNotEmpty()
            ? round($gradedSubmissions->avg('grade'), 1)
            : 0;

        // Per-milestone completion stats
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
                    ? round(($submitted / $totalStudents) * 100)
                    : 0,
            ];
        });

        // Per-student stats
        $studentStats = $students->map(function ($student) use ($totalMilestones) {
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
                    ? round($graded->avg('grade'), 1)
                    : null,
                'completion_rate'  => $totalMilestones > 0
                    ? round(($submissions->count() / $totalMilestones) * 100)
                    : 0,
            ];
        });

        // Supervisor workload scoped to this cohort + year of study
        $supervisorWorkload = Supervisor::whereHas('projects.student.user',
                fn($q) => $q->where('department_id', $deptId))
            ->with(['user', 'projects' => fn($q) =>
                $q->whereHas('student', fn($sq) =>
                    $sq->whereHas('user', fn($uq) => $uq->where('department_id', $deptId))
                       ->where('intake_year', $selectedYear)
                       ->where('year_of_study', $selectedYearOfStudy)
                )->with(['student.milestoneSubmissions' => fn($mq) =>
                    $mq->where('is_latest', true)
                ])
            ])
            ->get()
            ->map(function ($supervisor) {
                $submissions = $supervisor->projects->flatMap(
                    fn($p) => $p->student->milestoneSubmissions ?? collect()
                );

                $graded  = $submissions->where('status', 'graded');
                $pending = $submissions->where('status', 'submitted')->count();

                return [
                    'name'          => $supervisor->user->full_name,
                    'student_count' => $supervisor->projects->count(),
                    'reviewed'      => $submissions->whereIn('status', ['supervisor_approved', 'graded'])->count(),
                    'pending'       => $pending,
                    'avg_grade'     => $graded->isNotEmpty()
                        ? round($graded->avg('grade'), 1)
                        : null,
                ];
            })
            ->filter(fn($s) => $s['student_count'] > 0)
            ->values();

        return view('department-coordinator.reports.index', compact(
            'cohortYears', 'yearsOfStudy', 'selectedYear', 'selectedYearOfStudy',
            'totalStudents', 'totalMilestones', 'lateSubmissionsCount', 'overallAverage',
            'milestoneStats', 'studentStats', 'supervisorWorkload'
        ));
    }

    private function exportCsv(int $deptId, mixed $selectedYear, mixed $selectedYearOfStudy)
    {
        $students = Student::whereHas('user', fn($q) => $q->where('department_id', $deptId))
            ->where('intake_year', $selectedYear)
            ->where('year_of_study', $selectedYearOfStudy)
            ->with([
                'user',
                'milestoneSubmissions' => fn($q) => $q->where('is_latest', true),
                'projects.supervisor.user',
            ])
            ->get();

        $milestones = Milestone::where('department_id', $deptId)
            ->where('intake_year', $selectedYear)
            ->where('year_of_study', $selectedYearOfStudy)
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
                    ? round(($submissions->count() / $totalMilestones) * 100) . '%'
                    : '0%',
            ];
        });

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        foreach ($rows as $row) fputcsv($handle, $row);
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        $filename = "report_{$selectedYear}_year{$selectedYearOfStudy}_"
            . now()->format('Ymd') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
