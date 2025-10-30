<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseUser extends Model
{
    use HasFactory;

    protected $table = 'course_users';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $fillable = ['user_id', 'course_id', 'permission'];
}
