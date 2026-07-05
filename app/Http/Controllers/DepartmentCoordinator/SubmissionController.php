<?php
namespace App\Http\Controllers\DepartmentCoordinator;
use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\MilestoneSubmission;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $deptId = Auth::user()->department_id;

        // Derive cohort options from milestones themselves (not students),
        // so a milestone for a brand-new cohort is always selectable —
        // even before any student in that cohort exists in the Student table.
        $cohortRows = Milestone::where('department_id', $deptId)
            ->select('intake_year', 'year_of_study')
            ->distinct()
            ->orderByDesc('intake_year')
            ->orderBy('year_of_study')
            ->get();

        $intakeYears = $cohortRows->pluck('intake_year')->unique()->sortDesc()->values();

        $selectedYear = $request->input('intake_year', $intakeYears->first());

        $yearsOfStudy = $cohortRows
            ->where('intake_year', $selectedYear)
            ->pluck('year_of_study')
            ->unique()
            ->sort()
            ->values();

        $selectedYearOfStudy = $request->input('year_of_study', $yearsOfStudy->first());

        $milestones = Milestone::with([
            'submissions' => function ($query) {
                $query->where('is_latest', true)->with('student.user');
            }
        ])
        ->where('department_id', $deptId)
        ->when($selectedYear, fn($q) => $q->where('intake_year', $selectedYear))
        ->when($selectedYearOfStudy, fn($q) => $q->where('year_of_study', $selectedYearOfStudy))
        ->orderBy('sequence_order')
        ->get();

        return view('department-coordinator.submissions.index', compact(
            'milestones', 'intakeYears', 'yearsOfStudy', 'selectedYear', 'selectedYearOfStudy'
        ));
    }
    public function progress(Request $request)
    {
        $deptId = Auth::user()->department_id;

        $cohortRows = Milestone::where('department_id', $deptId)
            ->select('intake_year', 'year_of_study')
            ->distinct()
            ->orderByDesc('intake_year')
            ->orderBy('year_of_study')
            ->get();

        $intakeYears = $cohortRows->pluck('intake_year')->unique()->sortDesc()->values();
        $selectedYear = $request->input('intake_year', $intakeYears->first());

        $yearsOfStudy = $cohortRows
            ->where('intake_year', $selectedYear)
            ->pluck('year_of_study')
            ->unique()
            ->sort()
            ->values();
        $selectedYearOfStudy = $request->input('year_of_study', $yearsOfStudy->first());

        $milestones = Milestone::where('department_id', $deptId)
            ->when($selectedYear, fn($q) => $q->where('intake_year', $selectedYear))
            ->when($selectedYearOfStudy, fn($q) => $q->where('year_of_study', $selectedYearOfStudy))
            ->orderBy('sequence_order')
            ->get();

        $students = User::where('role', 'student')
            ->where('department_id', $deptId)
            ->whereHas('student', function ($q) use ($selectedYear, $selectedYearOfStudy) {
                $q->where('intake_year', $selectedYear)
                  ->where('year_of_study', $selectedYearOfStudy);
            })
            ->with('student')
            ->get();

        $allSubmissions = MilestoneSubmission::where('is_latest', true)
            ->whereIn('milestone_id', $milestones->pluck('id'))
            ->get()
            ->groupBy('student_id');

        return view('department-coordinator.submissions.progress', compact(
            'milestones', 'students', 'allSubmissions',
            'intakeYears', 'yearsOfStudy', 'selectedYear', 'selectedYearOfStudy'
        ));
    }
    public function download(MilestoneSubmission $submission)
    {
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
