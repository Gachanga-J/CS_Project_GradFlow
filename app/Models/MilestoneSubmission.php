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
        'submitted_late',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at'   => 'datetime',
            'is_latest'      => 'boolean',
            'submitted_late' => 'boolean',
        ];
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Human-readable file size (e.g. "1.2 MB", "340 KB").
     */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size_bytes ?? 0;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }
}
