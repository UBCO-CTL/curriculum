<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OptionalPriorities extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use \Backpack\CRUD\app\Models\Traits\HasIdentifiableAttribute;
    use HasFactory;

    protected $table = 'optional_priorities';

    protected $primaryKey = 'op_id';

    //protected $guarded = ['op_id']; //its guarded or fillable, one or the other
    protected $fillable = ['op_id', 'subcat_id', 'optional_priority', 'isCheckable'];

    public function optionalPrioritySubcategory(): BelongsTo
    {
        return $this->belongsTo(OptionalPrioritySubcategories::class, 'subcat_id', 'subcat_id');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_optional_priorities', 'op_id', 'course_id');
    }

    public function optionalPrioritySubdescription(): HasOne
    {
        return $this->hasOne(optionalPrioritySubdescription::class, 'op_subdesc', 'op_subdesc');
    }
}
