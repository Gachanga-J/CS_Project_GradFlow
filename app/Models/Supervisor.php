<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supervisor extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'staff_number', 'max_student_capacity', 'current_load'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ResearchTag::class, 'supervisor_research_tag');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function hasCapacity(): bool
    {
        return $this->current_load < $this->max_student_capacity;
    }
}
