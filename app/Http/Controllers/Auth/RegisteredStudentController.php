<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredStudentController extends Controller
{
    public function create(): View
    {
        $departments = Department::orderBy('name')->get();
        $years       = range(now()->year - 5, now()->year);

        return view('auth.register-student', compact('departments', 'years'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:80'],
            'last_name'     => ['required', 'string', 'max:80'],
            'email'         => [
                'required', 'string', 'lowercase', 'email', 'max:150',
                Rule::unique(User::class, 'email'),
                function ($attribute, $value, $fail) use ($request) {
                    $expected = User::generateEmail($request->first_name, $request->last_name, 'stu');
                    if (strtolower($value) !== $expected) {
                        $fail("Your email must be {$expected} (based on your name).");
                    }
                },
            ],
            'department_id' => ['required', 'exists:departments,id'],
            'reg_number'    => ['required', 'string', 'max:30', Rule::unique('students', 'reg_number')],
            'year_of_study' => ['required', 'integer', 'between:1,6'],
            'intake_year'   => ['required', 'integer', 'min:2000', 'max:' . now()->year],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name'    => $validated['first_name'],
            'last_name'     => $validated['last_name'],
            'email'         => $validated['email'],
            'password'      => $validated['password'],
            'role'          => 'student',
            'department_id' => $validated['department_id'],
            'is_active'     => true,
        ]);

        Student::create([
            'user_id'       => $user->id,
            'reg_number'    => $validated['reg_number'],
            'year_of_study' => $validated['year_of_study'] ?? null,
            'intake_year'   => $validated['intake_year'],
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard.student', absolute: false));
    }
}
