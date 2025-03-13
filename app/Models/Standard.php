<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Standard extends Model
{
    use HasFactory;

    protected $primaryKey = 'standard_id';

    protected $fillable = ['standard_id', 'standard_category_id', 's_shortphrase', 's_outcome'];

    public function learningOutcomes(): BelongsToMany
    {
        return $this->belongsToMany(LearningOutcome::class)->using(StandardsOutcomeMap::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'standard_category_id', 'standard_category_id');
    }
}
