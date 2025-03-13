<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LearningActivity extends Model
{
    use HasFactory;

    protected $primaryKey = 'l_activity_id';

    protected $fillable = ['l_activity', 'course_id'];

    public function learningOutcomes(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\LearningOutcome::class)->using(\App\Models\OutcomeActivity::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Course::class);
    }
}
