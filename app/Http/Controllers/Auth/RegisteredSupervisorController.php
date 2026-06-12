<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredSupervisorController extends Controller
{
    /**
     * Display the supervisor registration view.
     */
    public function create(): View
    {
        return view('auth.register-supervisor');
    }

    /**
     * Handle an incoming supervisor registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:150',
                Rule::unique(User::class, 'email'),
                function ($attribute, $value, $fail) use ($request) {
                    $expected = User::generateEmail($request->first_name, $request->last_name, 'sup');
                    if (strtolower($value) !== $expected) {
                        $fail("Your email must be {$expected} (based on your name).");
                    }
                },
            ],
            'staff_number' => ['nullable', 'string', 'max:30'],
            'max_student_capacity' => ['nullable', 'integer', 'between:1,255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        Supervisor::create([
            'user_id' => $user->id,
            'staff_number' => $validated['staff_number'] ?? null,
            'max_student_capacity' => $validated['max_student_capacity'] ?? 5,
            'current_load' => 0,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard.supervisor', absolute: false));
    }
}