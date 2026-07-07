<?php

namespace App\Http\Controllers\DepartmentCoordinator;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\Student;
use App\Services\DeadlineReminderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneController extends Controller
{
    private function departmentId(): int
    {
        return Auth::user()->department_id;
    }

    private function cohortOptions(): array
    {
        // Returns [intake_year => [year_of_study, ...]] for students in this department
        $students = Student::whereHas('user', fn($q) => $q->where('department_id', $this->departmentId()))
            ->whereNotNull('intake_year')
            ->whereNotNull('year_of_study')
            ->get(['intake_year', 'year_of_study'])
            ->groupBy('intake_year')
            ->map(fn($group) => $group->pluck('year_of_study')->unique()->sort()->values())
            ->sortKeysDesc();

        return $students->toArray();
    }

    public function index()
    {
        $milestones = Milestone::where('department_id', $this->departmentId())
            ->orderBy('intake_year', 'desc')
            ->orderBy('year_of_study')
            ->orderBy('sequence_order')
            ->get()
            ->groupBy(fn($m) => $m->intake_year . '|' . $m->year_of_study);

        $cohortOptions = $this->cohortOptions();

        return view('department-coordinator.milestones.index', compact('milestones', 'cohortOptions'));
    }

    public function create()
    {
        $cohortOptions = $this->cohortOptions();
        return view('department-coordinator.milestones.create', compact('cohortOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'          => ['required', 'string', 'max:150'],
            'description'    => ['nullable', 'string'],
            'deadline'       => ['nullable', 'date'],
            'sequence_order' => ['required', 'integer', 'min:1'],
            'intake_year'    => ['required', 'integer', 'min:2000', 'max:' . now()->year],
            'year_of_study'  => ['required', 'integer', 'min:1', 'max:6'],
        ]);

        Milestone::create([
            'created_by'     => Auth::id(),
            'department_id'  => $this->departmentId(),
            'intake_year'    => $request->intake_year,
            'year_of_study'  => $request->year_of_study,
            'title'          => $request->title,
            'description'    => $request->description,
            'deadline'       => $request->deadline,
            'status'         => 'open',
            'sequence_order' => $request->sequence_order,
        ]);

        return redirect()->route('department-coordinator.milestones.index')
            ->with('success', 'Milestone created successfully!');
    }

    public function edit(Milestone $milestone)
    {
        abort_if($milestone->department_id !== $this->departmentId(), 403);
        $cohortOptions = $this->cohortOptions();
        return view('department-coordinator.milestones.edit', compact('milestone', 'cohortOptions'));
    }

    public function update(Request $request, Milestone $milestone)
    {
        abort_if($milestone->department_id !== $this->departmentId(), 403);

        $request->validate([
            'title'          => ['required', 'string', 'max:150'],
            'description'    => ['nullable', 'string'],
            'deadline'       => ['nullable', 'date'],
            'sequence_order' => ['required', 'integer', 'min:1'],
            'intake_year'    => ['required', 'integer', 'min:2000', 'max:' . now()->year],
            'year_of_study'  => ['required', 'integer', 'min:1', 'max:6'],
        ]);

        $milestone->update($request->only(
            'title', 'description', 'deadline', 'sequence_order', 'intake_year', 'year_of_study'
        ));

        return redirect()->route('department-coordinator.milestones.index')
            ->with('success', 'Milestone updated successfully!');
    }

    public function toggleStatus(Milestone $milestone)
    {
        abort_if($milestone->department_id !== $this->departmentId(), 403);

        $milestone->update([
            'status' => $milestone->status === 'open' ? 'closed' : 'open',
        ]);

        return back()->with('success', 'Milestone status updated.');
    }

    public function destroy(Milestone $milestone)
    {
        abort_if($milestone->department_id !== $this->departmentId(), 403);
        $milestone->delete();

        return redirect()->route('department-coordinator.milestones.index')
            ->with('success', 'Milestone deleted.');
    }

    public function sendReminders(DeadlineReminderService $reminderService)
    {
        $sent = $reminderService->sendAll($this->departmentId());
        return back()->with('success', "Sent {$sent} reminder notification(s) to students in your department.");
    }
}
