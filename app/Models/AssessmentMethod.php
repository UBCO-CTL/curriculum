<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AssessmentMethod extends Model
{
    use HasFactory;

    protected $primaryKey = 'a_method_id';

    protected $fillable = ['a_method', 'weight', 'course_id'];

    public function learningOutcomes(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\LearningOutcome::class)->using(\App\Models\OutcomeAssessment::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Course::class);
    }
}
