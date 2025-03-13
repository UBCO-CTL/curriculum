<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campus extends Model
{
    use HasFactory;

    protected $table = 'campuses';

    protected $primaryKey = 'campus_id';

    protected $guarded = 'campus_id';

    protected $fillable = ['campus'];

    public function faculties(): HasMany
    {
        return $this->hasMany(faculty::class, 'campus_id', 'campus_id');
    }
}
