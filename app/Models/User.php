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

#[Fillable(['first_name', 'last_name', 'email', 'password', 'role', 'department_id', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function supervisor(): HasOne
    {
        return $this->hasOne(Supervisor::class);
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    public function isSystemAdministrator(): bool
    {
        return $this->role === 'system_administrator';
    }

    public function isDepartmentCoordinator(): bool
    {
        return $this->role === 'department_coordinator';
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'student'                => 'dashboard.student',
            'supervisor'             => 'dashboard.supervisor',
            'department_coordinator' => 'dashboard.department-coordinator',
            'system_administrator'   => 'dashboard.system-administrator',
            default                  => 'login',
        };
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public static function generateEmail(string $firstName, string $lastName, string $roleSuffix): string
    {
        $normalized = strtolower(preg_replace('/[^A-Za-z]/', '', $firstName . $lastName));
        return "{$normalized}.{$roleSuffix}@gradflow.com";
    }
}
