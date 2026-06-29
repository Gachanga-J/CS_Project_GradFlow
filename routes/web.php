<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // ── Dashboards ────────────────────────────────────────────────────────────
    Route::get('/dashboard/student', function () {
        return view('dashboard.student');
    })->middleware('role:student')->name('dashboard.student');

    Route::get('/dashboard/supervisor', function () {
        return view('dashboard.supervisor');
    })->middleware('role:supervisor')->name('dashboard.supervisor');

    Route::get('/dashboard/department-coordinator', function () {
        return view('dashboard.department-coordinator');
    })->middleware('role:department_coordinator')->name('dashboard.department-coordinator');

    Route::get('/dashboard/system-administrator', function () {
        return view('dashboard.system-administrator');
    })->middleware('role:system_administrator')->name('dashboard.system-administrator');

    // ── Student Routes ────────────────────────────────────────────────────────
    Route::prefix('student')->name('student.')->middleware('role:student')->group(function () {
        Route::get('/milestones/{milestone}', [App\Http\Controllers\Student\MilestoneSubmissionController::class, 'show'])->name('milestones.show');
        Route::post('/milestones/{milestone}/submit', [App\Http\Controllers\Student\MilestoneSubmissionController::class, 'store'])->name('milestones.submit');
        Route::get('/supervisor-matches', [App\Http\Controllers\Student\MilestoneSubmissionController::class, 'supervisorMatches'])->name('supervisor-matches');
    });

    // ── Supervisor Routes ─────────────────────────────────────────────────────
    Route::prefix('supervisor')->name('supervisor.')->middleware('role:supervisor')->group(function () {
        Route::get('/submissions', [App\Http\Controllers\Supervisor\SubmissionController::class, 'index'])->name('submissions.index');
        Route::post('/submissions/{submission}/approve', [App\Http\Controllers\Supervisor\SubmissionController::class, 'approve'])->name('submissions.approve');
        Route::post('/submissions/{submission}/reject', [App\Http\Controllers\Supervisor\SubmissionController::class, 'reject'])->name('submissions.reject');
        Route::get('/submissions/{submission}/download', [App\Http\Controllers\Supervisor\SubmissionController::class, 'download'])->name('submissions.download');

        Route::get('/students', [App\Http\Controllers\Supervisor\StudentController::class, 'index'])->name('students.index');

        Route::get('/profile', [App\Http\Controllers\Supervisor\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Supervisor\ProfileController::class, 'update'])->name('profile.update');
    });

    // ── Department Coordinator Routes ─────────────────────────────────────────
    Route::prefix('department-coordinator')->name('department-coordinator.')->middleware('role:department_coordinator')->group(function () {
        Route::get('/milestones', [App\Http\Controllers\DepartmentCoordinator\MilestoneController::class, 'index'])->name('milestones.index');
        Route::get('/milestones/create', [App\Http\Controllers\DepartmentCoordinator\MilestoneController::class, 'create'])->name('milestones.create');
        Route::post('/milestones', [App\Http\Controllers\DepartmentCoordinator\MilestoneController::class, 'store'])->name('milestones.store');
        Route::get('/milestones/{milestone}/edit', [App\Http\Controllers\DepartmentCoordinator\MilestoneController::class, 'edit'])->name('milestones.edit');
        Route::put('/milestones/{milestone}', [App\Http\Controllers\DepartmentCoordinator\MilestoneController::class, 'update'])->name('milestones.update');
        Route::post('/milestones/{milestone}/toggle', [App\Http\Controllers\DepartmentCoordinator\MilestoneController::class, 'toggleStatus'])->name('milestones.toggle');
        Route::delete('/milestones/{milestone}', [App\Http\Controllers\DepartmentCoordinator\MilestoneController::class, 'destroy'])->name('milestones.destroy');

        Route::get('/submissions', [App\Http\Controllers\DepartmentCoordinator\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/{submission}/download', [App\Http\Controllers\DepartmentCoordinator\SubmissionController::class, 'download'])->name('submissions.download');
        Route::post('/submissions/{submission}/grade', [App\Http\Controllers\DepartmentCoordinator\SubmissionController::class, 'grade'])->name('submissions.grade');

        Route::get('/projects', [App\Http\Controllers\DepartmentCoordinator\ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/create', [App\Http\Controllers\DepartmentCoordinator\ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [App\Http\Controllers\DepartmentCoordinator\ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}/assign', [App\Http\Controllers\DepartmentCoordinator\ProjectController::class, 'assign'])->name('projects.assign');
        Route::post('/projects/{project}/assign', [App\Http\Controllers\DepartmentCoordinator\ProjectController::class, 'assignSupervisor'])->name('projects.assign.supervisor');
        Route::delete('/projects/{project}', [App\Http\Controllers\DepartmentCoordinator\ProjectController::class, 'destroy'])->name('projects.destroy');
    });

    // ── System Administrator Routes ───────────────────────────────────────────
    Route::prefix('system-administrator')->name('system-administrator.')->middleware('role:system_administrator')->group(function () {
        Route::get('/departments', [App\Http\Controllers\SystemAdministrator\DepartmentController::class, 'index'])->name('departments.index');
        Route::get('/departments/create', [App\Http\Controllers\SystemAdministrator\DepartmentController::class, 'create'])->name('departments.create');
        Route::post('/departments', [App\Http\Controllers\SystemAdministrator\DepartmentController::class, 'store'])->name('departments.store');
        Route::get('/departments/{department}/edit', [App\Http\Controllers\SystemAdministrator\DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('/departments/{department}', [App\Http\Controllers\SystemAdministrator\DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [App\Http\Controllers\SystemAdministrator\DepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::get('/users', [App\Http\Controllers\SystemAdministrator\UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [App\Http\Controllers\SystemAdministrator\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\SystemAdministrator\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [App\Http\Controllers\SystemAdministrator\DepartmentController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [App\Http\Controllers\SystemAdministrator\UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle', [App\Http\Controllers\SystemAdministrator\UserController::class, 'toggleActive'])->name('users.toggle');
        Route::post('/users/{user}/reset-password', [App\Http\Controllers\SystemAdministrator\UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::get('/tags', [App\Http\Controllers\SystemAdministrator\ResearchTagController::class, 'index'])->name('tags.index');
        Route::post('/tags', [App\Http\Controllers\SystemAdministrator\ResearchTagController::class, 'store'])->name('tags.store');
        Route::put('/tags/{tag}', [App\Http\Controllers\SystemAdministrator\ResearchTagController::class, 'update'])->name('tags.update');
        Route::delete('/tags/{tag}', [App\Http\Controllers\SystemAdministrator\ResearchTagController::class, 'destroy'])->name('tags.destroy');
    });

    // ── Notifications ─────────────────────────────────────────────────────────
    Route::prefix('notifications')->name('notifications.')->middleware('role:student')->group(function () {
        Route::get('/', function () {
            $notifications = auth()->user()->notifications()->latest()->paginate(20);
            return view('notifications.index', compact('notifications'));
        })->name('index');

        Route::post('/{id}/read', function (string $id) {
            auth()->user()->notifications()->findOrFail($id)->markAsRead();
            return back();
        })->name('read');

        Route::post('/read-all', function () {
            auth()->user()->unreadNotifications->markAsRead();
            return back();
        })->name('read-all');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';