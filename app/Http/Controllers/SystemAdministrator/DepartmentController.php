<?php

namespace App\Http\Controllers\SystemAdministrator;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * Show all departments.
     */
    public function index(): View
    {
        $departments = Department::withCount('users')->orderBy('name')->get();

        return view('system-administrator.departments.index', compact('departments'));
    }

    /**
     * Show the form to create a new department.
     */
    public function create(): View
    {
        return view('system-administrator.departments.create');
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:departments,name'],
        ]);

        Department::create([
            'name' => $request->name,
        ]);

        return redirect()->route('system-administrator.departments.index')
            ->with('success', 'Department created successfully!');
    }

    /**
     * Show the form to edit a department.
     */
    public function edit(Department $department): View
    {
        return view('system-administrator.departments.edit', compact('department'));
    }

    /**
     * Update a department.
     */
    public function update(Request $request, Department $department): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:departments,name,' . $department->id],
        ]);

        $department->update([
            'name' => $request->name,
        ]);

        return redirect()->route('system-administrator.departments.index')
            ->with('success', 'Department updated successfully!');
    }

    /**
     * Delete a department, blocked if users are still assigned to it.
     */
    public function destroy(Department $department): RedirectResponse
    {
        if ($department->users()->exists()) {
            return back()->with('error', 'Cannot delete this department while users are still assigned to it. Reassign or remove them first.');
        }

        $department->delete();

        return back()->with('success', 'Department deleted.');
    }
}