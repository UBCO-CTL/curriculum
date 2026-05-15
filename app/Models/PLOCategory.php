<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PLOCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'plo_category_id';

    protected $fillable = ['program_id', 'plo_category', 'position'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function ($builder) {
            $builder->orderBy('p_l_o_categories.position', 'asc')
                ->orderBy('p_l_o_categories.plo_category_id', 'asc');
        });
    }

    public function plos(): HasMany
    {
        return $this->hasMany(ProgramLearningOutcome::class, 'plo_category_id', 'plo_category_id');
    }
}
