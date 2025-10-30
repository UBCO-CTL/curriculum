<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MappingScaleProgram extends Model
{
    use HasFactory;

    protected $primaryKey = ['map_scale_id', 'program_id'];

    public $incrementing = false;

    protected $fillable = ['map_scale_id', 'program_id'];

    public function mappingScales(): BelongsTo
    {
        return $this->belongsTo(MappingScale::class);
    }

    public function programs(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
