<?php

namespace App\Http\Controllers\SystemAdministrator;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Roles that can be created/edited through this admin panel.
     */
    private const MANAGEABLE_ROLES = ['department_coordinator', 'system_administrator'];

    /**
     * Show all users, optionally filtered by role and/or department.
     */
    public function index(Request $request): View
    {
        $users = User::with('department')
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', $request->role);
            })
            ->when($request->filled('department_id'), function ($query) use ($request) {
                $query->where('department_id', $request->department_id);
            })
            ->orderBy('first_name')
            ->get();

        $departments = Department::orderBy('name')->get();

        return view('system-administrator.users.index', compact('users', 'departments'));
    }

    /**
     * Show the form to create a new Department Coordinator or System Administrator.
     */
    public function create(): View
    {
        $departments = Department::orderBy('name')->get();

        return view('system-administrator.users.create', compact('departments'));
    }

    /**
     * Store a newly created user with a random one-time password.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name'    => ['required', 'string', 'max:80'],
            'last_name'     => ['required', 'string', 'max:80'],
            'role'          => ['required', 'string', 'in:' . implode(',', self::MANAGEABLE_ROLES)],
            'department_id' => ['nullable', 'required_if:role,department_coordinator', 'exists:departments,id'],
        ]);

        $roleSuffix = $request->role === 'department_coordinator' ? 'dc' : 'sysadm';
        $email = User::generateEmail($request->first_name, $request->last_name, $roleSuffix);
        $temporaryPassword = Str::random(12);

        User::create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $email,
            'password'          => $temporaryPassword,
            'role'              => $request->role,
            'department_id'     => $request->role === 'department_coordinator' ? $request->department_id : null,
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('system-administrator.users.index')
            ->with('success', "User created! Email: {$email} — Temporary password: {$temporaryPassword} (copy this now, it won't be shown again).");
    }

    /**
     * Show the form to edit a Department Coordinator or System Administrator.
     */
    public function edit(User $user): View
    {
        abort_unless(in_array($user->role, self::MANAGEABLE_ROLES, true), 403, 'This user cannot be edited here.');

        $departments = Department::orderBy('name')->get();

        return view('system-administrator.users.edit', compact('user', 'departments'));
    }

    /**
     * Update a user's role and/or department.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, self::MANAGEABLE_ROLES, true), 403, 'This user cannot be edited here.');

        $request->validate([
            'role'          => ['required', 'string', 'in:' . implode(',', self::MANAGEABLE_ROLES)],
            'department_id' => ['nullable', 'required_if:role,department_coordinator', 'exists:departments,id'],
        ]);

        $user->update([
            'role'          => $request->role,
            'department_id' => $request->role === 'department_coordinator' ? $request->department_id : null,
        ]);

        return redirect()->route('system-administrator.users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Toggle a user's active status. Admins cannot deactivate their own account.
     */
    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        return back()->with('success', $user->is_active ? 'User activated.' : 'User deactivated.');
    }
}
