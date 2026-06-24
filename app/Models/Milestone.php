<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'department_id',
        'title',
        'description',
        'deadline',
        'status',
        'sequence_order',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(MilestoneSubmission::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function submissionByStudent(int $studentId): ?MilestoneSubmission
    {
        return $this->submissions()
            ->where('student_id', $studentId)
            ->where('is_latest', true)
            ->first();
    }
}
