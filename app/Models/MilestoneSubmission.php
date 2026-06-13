<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilestoneSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'milestone_id',
        'student_id',
        'file_path',
        'file_name',
        'file_size_bytes',
        'mime_type',
        'version_number',
        'is_latest',
        'status',
        'supervisor_feedback',
        'admin_feedback',
        'grade',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'is_latest'    => 'boolean',
            'grade'        => 'integer',
        ];
    }

    /**
     * The milestone this submission belongs to.
     */
    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    /**
     * The student who made this submission.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get human-readable file size.
     */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size_bytes;
        if ($bytes < 1024) return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    /**
     * Check if approved by supervisor.
     */
    public function isSupervisorApproved(): bool
    {
        return $this->status === 'supervisor_approved';
    }

    /**
     * Check if rejected by supervisor.
     */
    public function isSupervisorRejected(): bool
    {
        return $this->status === 'supervisor_rejected';
    }

    /**
     * Check if graded by admin.
     */
    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }
}