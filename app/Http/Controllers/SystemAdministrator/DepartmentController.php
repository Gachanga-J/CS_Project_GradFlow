<?php

namespace App\Http\Controllers\SystemAdministrator;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
     * Store a newly created department, optionally with a coordinator created at the same time.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'                     => ['required', 'string', 'max:150', 'unique:departments,name'],
            'create_coordinator'       => ['nullable', 'boolean'],
            'coordinator_first_name'   => ['required_if:create_coordinator,1', 'nullable', 'string', 'max:80'],
            'coordinator_last_name'    => ['required_if:create_coordinator,1', 'nullable', 'string', 'max:80'],
        ]);

        $successMessage = DB::transaction(function () use ($request) {
            $department = Department::create([
                'name' => $request->name,
            ]);

            if (! $request->boolean('create_coordinator')) {
                return 'Department created successfully!';
            }

            $email = User::generateEmail($request->coordinator_first_name, $request->coordinator_last_name, 'dc');
            $temporaryPassword = Str::random(12);

            User::create([
                'first_name'        => $request->coordinator_first_name,
                'last_name'         => $request->coordinator_last_name,
                'email'             => $email,
                'password'          => $temporaryPassword,
                'role'              => 'department_coordinator',
                'department_id'     => $department->id,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            return "Department created with coordinator! Email: {$email} — Temporary password: {$temporaryPassword} (copy this now, it won't be shown again).";
        });

        return redirect()->route('system-administrator.departments.index')
            ->with('success', $successMessage);
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
