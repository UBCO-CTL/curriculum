<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseProgram extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'program_id', 'course_required', 'instructor_assigned', 'map_status'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }
}
