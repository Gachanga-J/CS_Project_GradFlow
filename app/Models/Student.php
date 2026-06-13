<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'reg_number', 'year_of_study'];

    /**
     * The user account this student profile belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * All projects belonging to this student.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * All milestone submissions made by this student.
     */
    public function milestoneSubmissions(): HasMany
    {
        return $this->hasMany(MilestoneSubmission::class);
    }
}