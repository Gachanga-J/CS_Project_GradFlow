<?php

namespace App\Http\Controllers\DepartmentCoordinator;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneController extends Controller
{
    private function departmentId(): int
    {
        return Auth::user()->department_id;
    }

    public function index()
    {
        $milestones = Milestone::where('department_id', $this->departmentId())
            ->orderBy('sequence_order')
            ->get();

        return view('department-coordinator.milestones.index', compact('milestones'));
    }

    public function create()
    {
        return view('department-coordinator.milestones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'          => ['required', 'string', 'max:150'],
            'description'    => ['nullable', 'string'],
            'deadline'       => ['nullable', 'date'],
            'sequence_order' => ['required', 'integer', 'min:1'],
        ]);

        Milestone::create([
            'created_by'     => Auth::id(),
            'department_id'  => $this->departmentId(),
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

        return view('department-coordinator.milestones.edit', compact('milestone'));
    }

    public function update(Request $request, Milestone $milestone)
    {
        abort_if($milestone->department_id !== $this->departmentId(), 403);

        $request->validate([
            'title'          => ['required', 'string', 'max:150'],
            'description'    => ['nullable', 'string'],
            'deadline'       => ['nullable', 'date'],
            'sequence_order' => ['required', 'integer', 'min:1'],
        ]);

        $milestone->update($request->only('title', 'description', 'deadline', 'sequence_order'));

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
}
