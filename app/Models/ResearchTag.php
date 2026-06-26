<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ResearchTag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(Supervisor::class, 'supervisor_research_tag');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_research_tag');
    }
}
