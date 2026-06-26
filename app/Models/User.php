<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @method \Illuminate\Notifications\DatabaseNotificationCollection notifications()
 * @method \Illuminate\Notifications\DatabaseNotificationCollection unreadNotifications()
 */
#[Fillable(['first_name', 'last_name', 'email', 'password', 'role', 'department_id', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * The department this user belongs to (nullable for system administrators).
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * The student profile linked to this user (if role = student).
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * The supervisor profile linked to this user (if role = supervisor).
     */
    public function supervisor(): HasOne
    {
        return $this->hasOne(Supervisor::class);
    }

    /**
     * Check if the user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Get the name of this user's dashboard route, based on their role.
     */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'student' => 'dashboard.student',
            'supervisor' => 'dashboard.supervisor',
            'department_coordinator' => 'dashboard.department-coordinator',
            'system_administrator' => 'dashboard.system-administrator',
            default => 'login',
        };
    }

    /**
     * Check if the user is a supervisor.
     */
    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    /**
     * Check if the user is a department coordinator.
     */
    public function isDepartmentCoordinator(): bool
    {
        return $this->role === 'department_coordinator';
    }

    /**
     * Check if the user is a system administrator.
     */
    public function isSystemAdministrator(): bool
    {
        return $this->role === 'system_administrator';
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Generate the expected GradFlow email for a given name and role.
     *
     * Example: generateEmail('Jane', 'Doe', 'stu') => 'janedoe.stu@gradflow.com'
     */
    public static function generateEmail(string $firstName, string $lastName, string $roleSuffix): string
    {
        $normalized = strtolower(preg_replace('/[^A-Za-z]/', '', $firstName . $lastName));

        return "{$normalized}.{$roleSuffix}@gradflow.com";
    }
}