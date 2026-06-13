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

    /**
     * The admin who created this milestone.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * All submissions for this milestone.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(MilestoneSubmission::class);
    }

    /**
     * Check if this milestone is open for submission.
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Get the submission for a specific student.
     */
    public function submissionByStudent(int $studentId): ?MilestoneSubmission
    {
        return $this->submissions()
            ->where('student_id', $studentId)
            ->where('is_latest', true)
            ->first();
    }
}