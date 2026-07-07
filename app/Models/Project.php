<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'supervisor_id',
        'title',
        'status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ResearchTag::class, 'project_research_tag');
    }

    /**
     * Compute a Jaccard-style compatibility score (0–100) against a supervisor.
     */
    public function compatibilityScore(Supervisor $supervisor): int
    {
        $projectTagIds    = $this->tags->pluck('id');
        $supervisorTagIds = $supervisor->tags->pluck('id');

        if ($projectTagIds->isEmpty() && $supervisorTagIds->isEmpty()) {
            return 0;
        }

        $intersection = $projectTagIds->intersect($supervisorTagIds)->count();
        $union        = $projectTagIds->union($supervisorTagIds)->count();

        return $union > 0 ? (int) round(($intersection / $union) * 100) : 0;
    }
}
