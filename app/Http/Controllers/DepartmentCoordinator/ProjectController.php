<?php

namespace App\Http\Controllers\DepartmentCoordinator;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ResearchTag;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    private function departmentId(): int
    {
        return Auth::user()->department_id;
    }

    public function index()
    {
        $projects = Project::with(['student.user', 'supervisor.user', 'tags'])
            ->whereHas('student.user', fn($q) => $q->where('department_id', $this->departmentId()))
            ->orderByDesc('created_at')
            ->get();

        return view('department-coordinator.projects.index', compact('projects'));
    }

    public function create()
    {
        $students = Student::with('user')
            ->whereHas('user', fn($q) => $q->where('department_id', $this->departmentId())
                ->where('is_active', true))
            ->whereDoesntHave('projects', fn($q) => $q->where('status', 'active'))
            ->get()
            ->sortBy('user.first_name');

        $tags = ResearchTag::orderBy('name')->get();

        return view('department-coordinator.projects.create', compact('students', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'tags'       => ['nullable', 'array'],
            'tags.*'     => ['integer', 'exists:research_tags,id'],
        ]);

        // Ensure student belongs to this department
        $student = Student::with('user')->findOrFail($request->student_id);
        abort_if($student->user->department_id !== $this->departmentId(), 403);

        // Prevent creating a second active project for a student who already has one
        $existingActive = $student->projects()->where('status', 'active')->exists();
        abort_if($existingActive, 422, 'This student already has an active project. Close or reassign the existing project first.');

        $project = Project::create([
            'student_id'    => $request->student_id,
            'supervisor_id' => null,
            'title'         => $request->title,
            'status'        => 'active',
        ]);

        $project->tags()->sync($request->tags ?? []);

        return redirect()->route('department-coordinator.projects.assign', $project)
            ->with('success', 'Project created. Now assign a supervisor.');
    }

    /**
     * Show supervisor assignment page with compatibility scores.
     */
    public function assign(Project $project)
    {
        abort_if(
            $project->student->user->department_id !== $this->departmentId(),
            403
        );

        $project->load('tags');

        $supervisors = Supervisor::with(['user', 'tags'])
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->get()
            ->map(function ($supervisor) use ($project) {
                $supervisor->compatibility = $project->compatibilityScore($supervisor);
                return $supervisor;
            })
            ->sortByDesc('compatibility')
            ->values();

        return view('department-coordinator.projects.assign', compact('project', 'supervisors'));
    }

    /**
     * Assign a supervisor to a project.
     */
    public function assignSupervisor(Request $request, Project $project)
    {
        abort_if(
            $project->student->user->department_id !== $this->departmentId(),
            403
        );

        $request->validate([
            'supervisor_id' => ['required', 'integer', 'exists:supervisors,id'],
        ]);

        $supervisor = Supervisor::findOrFail($request->supervisor_id);
        abort_unless($supervisor->hasCapacity(), 422, 'This supervisor has reached their student capacity.');

        // Decrement old supervisor load if reassigning
        if ($project->supervisor_id && $project->supervisor_id !== $supervisor->id) {
            $project->supervisor->decrement('current_load');
        }

        $project->update(['supervisor_id' => $supervisor->id]);

        // Only increment if newly assigned (not already assigned)
        if (!$project->wasChanged('supervisor_id') === false) {
            $supervisor->increment('current_load');
        }

        return redirect()->route('department-coordinator.projects.index')
            ->with('success', 'Supervisor assigned successfully.');
    }

    public function destroy(Project $project)
    {
        abort_if(
            $project->student->user->department_id !== $this->departmentId(),
            403
        );

        if ($project->supervisor_id) {
            $project->supervisor->decrement('current_load');
        }

        $project->delete();

        return back()->with('success', 'Project deleted.');
    }
}
