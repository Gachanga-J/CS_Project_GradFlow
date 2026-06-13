<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneController extends Controller
{
    /**
     * Show all milestones.
     */
    public function index()
    {
        $milestones = Milestone::orderBy('sequence_order')->get();

        return view('admin.milestones.index', compact('milestones'));
    }

    /**
     * Show the form to create a new milestone.
     */
    public function create()
    {
        return view('admin.milestones.create');
    }

    /**
     * Store a newly created milestone.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'          => ['required', 'string', 'max:150'],
            'description'    => ['nullable', 'string', 'max:1000'],
            'deadline'       => ['nullable', 'date', 'after:today'],
            'sequence_order' => ['required', 'integer', 'min:1'],
        ]);

        Milestone::create([
            'created_by'     => Auth::id(),
            'title'          => $request->title,
            'description'    => $request->description,
            'deadline'       => $request->deadline,
            'sequence_order' => $request->sequence_order,
            'status'         => 'open',
        ]);

        return redirect()->route('admin.milestones.index')
            ->with('success', 'Milestone created successfully!');
    }

    /**
     * Close or reopen a milestone.
     */
    public function toggleStatus(Milestone $milestone)
    {
        $milestone->update([
            'status' => $milestone->status === 'open' ? 'closed' : 'open',
        ]);

        return back()->with('success', 'Milestone status updated.');
    }

    /**
     * Delete a milestone.
     */
    public function destroy(Milestone $milestone)
    {
        $milestone->delete();

        return back()->with('success', 'Milestone deleted.');
    }
}