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
}
