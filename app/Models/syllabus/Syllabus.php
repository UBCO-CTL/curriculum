<?php

namespace App\Models\syllabus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Syllabus extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'course_id'];

    protected $table = 'syllabi';

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'syllabi_users', 'syllabus_id', 'user_id')->withPivot('permission');
    }
}
