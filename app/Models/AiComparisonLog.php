<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiComparisonLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'comparison_program_id',
        'prompt_tokens',
        'completion_tokens',
        'model',
        'report_markdown',
        'comparison_program_name',
    ];

    public function referenceProgram()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function comparisonProgram()
    {
        return $this->belongsTo(Program::class, 'comparison_program_id', 'program_id');
    }
}

