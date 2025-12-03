<?php

namespace App\Services\AI;

use App\Models\Program;
use Illuminate\Support\Str;

class ProgramComparisonFormatter
{
    private const MAX_PLOS = 60;
    private const MAX_COURSES = 60;
    private const MAX_FIELD_LENGTH = 5000;

    public function format(Program $program): array
    {
        return [
            'meta' => $this->formatMeta($program),
            'learning_outcomes' => $this->formatLearningOutcomes($program),
            'courses' => $this->formatCourses($program),
        ];
    }

    private function formatMeta(Program $program): array
    {
        return [
            'name' => $this->clean($program->program),
            'faculty' => $this->clean($program->faculty),
            'department' => $this->clean($program->department),
            'level' => $this->clean($program->level),
            'campus' => $this->clean($program->campus ?? null),
            'status' => $this->clean($program->status ?? null),
            'course_count' => $program->courses_count ?? $program->courses()->count(),
            'plo_count' => $program->program_learning_outcomes_count ?? $program->programLearningOutcomes()->count(),
        ];
    }

    private function formatLearningOutcomes(Program $program): array
    {
        return $program->programLearningOutcomes()
            ->with(['category'])
            ->withCount('learningOutcomes')
            ->limit(self::MAX_PLOS)
            ->get()
            ->map(function ($plo, int $index) use ($program) {
                $title = $plo->plo_shortphrase ?: 'PLO #'.($index + 1);

                return [
                    'title' => $this->clean($title),
                    'description' => $this->clean($plo->pl_outcome),
                    'category' => $this->clean(optional($plo->category)->plo_category),
                    'aligned_clo_count' => (int) $plo->learning_outcomes_count,
                ];
            })
            ->toArray();
    }

    private function formatCourses(Program $program): array
    {
        return $program->courses()
            ->withPivot(['course_required'])
            ->orderBy('course_code')
            ->orderBy('course_num')
            ->limit(self::MAX_COURSES)
            ->get()
            ->map(function ($course) {
                return [
                    'code' => $this->clean(trim($course->course_code.' '.$course->course_num), 120),
                    'title' => $this->clean($course->course_title, 300),
                    'level' => $this->deriveCourseLevel($course->course_num),
                    'required' => $course->pivot?->course_required === 1 ? 'Required' : 'Elective',
                ];
            })
            ->toArray();
    }

    private function deriveCourseLevel(?string $courseNumber): string
    {
        if (! $courseNumber) {
            return 'Unknown level';
        }

        $digits = preg_replace('/\D/', '', $courseNumber);
        $firstDigit = $digits !== '' ? (int) substr($digits, 0, 1) : null;

        return match ($firstDigit) {
            0, 1 => '100 level',
            2 => '200 level',
            3 => '300 level',
            4 => '400 level',
            5, 6 => 'Graduate level',
            default => 'Mixed level',
        };
    }

    private function clean(?string $value, int $limit = self::MAX_FIELD_LENGTH): string
    {
        if ($value === null) {
            return 'N/A';
        }

        $normalized = preg_replace('/\s+/u', ' ', trim((string) $value));

        return Str::limit($normalized, $limit, '…');
    }
}

