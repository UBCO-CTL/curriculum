<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// model for CourseLearningOutcomes
class LearningOutcome extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $table = 'learning_outcomes';

    protected $primaryKey = 'l_outcome_id';

    protected $fillable = ['l_outcome', 'clo_shortphrase', 'course_id'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Course::class, 'course_id');
    }

    public function assessmentMethods(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\AssessmentMethod::class, 'outcome_assessments', 'l_outcome_id', 'a_method_id')->using(\App\Models\OutcomeAssessment::class)->withTimeStamps();
    }

    public function learningActivities(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\LearningActivity::class, 'outcome_activities', 'l_outcome_id', 'l_activity_id')->using(\App\Models\OutcomeActivity::class)->withTimeStamps();
    }

    public function programLearningOutcomes(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\ProgramLearningOutcome::class, 'outcome_maps', 'l_outcome_id', 'pl_outcome_id')->using(\App\Models\OutcomeMap::class)->withPivot('map_scale_id')->withTimeStamps();
    }

    public function outcomeMap(): BelongsToMany
    {
        return $this->belongsToMany(ProgramLearningOutcome::class, 'outcome_maps', 'l_outcome_id', 'pl_outcome_id')->using(OutcomeMap::class)->withPivot('map_scale_id')->withTimeStamps();
    }
}
