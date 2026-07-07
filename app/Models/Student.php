<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'reg_number', 'year_of_study', 'intake_year'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function milestoneSubmissions(): HasMany
    {
        return $this->hasMany(MilestoneSubmission::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
