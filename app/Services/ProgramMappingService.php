<?php

namespace App\Services;

use App\Models\CourseProgram;
use App\Models\OutcomeMap;
use App\Models\Program;
use Illuminate\Support\Facades\DB;

class ProgramMappingService
{
    /**
     * @param  array<string,mixed>  $attributes
     */
    public function attachCourseToProgram(Program $program, int $courseId, array $attributes = []): CourseProgram
    {
        return DB::transaction(function () use ($program, $courseId, $attributes) {
            $courseProgram = new CourseProgram;
            $courseProgram->course_id = $courseId;
            $courseProgram->program_id = $program->program_id;
            $courseProgram->course_required = $attributes['course_required'] ?? null;
            $courseProgram->instructor_assigned = $attributes['instructor_assigned'] ?? null;
            $courseProgram->map_status = $attributes['map_status'] ?? 0;
            $courseProgram->note = $attributes['note'] ?? null;
            $courseProgram->save();

            return $courseProgram;
        });
    }

    /**
     * Duplicate program learning outcome mappings for a cloned course.
     *
     * @param  array<int,int>  $programLearningOutcomeMap  Map of old PLO IDs to new PLO IDs.
     * @param  array<int,int>  $mappingScaleMap            Map of old mapping scale IDs to new IDs.
     */
    public function duplicateOutcomeMapsForCourse(
        array $programLearningOutcomeMap,
        array $mappingScaleMap,
        CourseCloneResult $cloneResult
    ): void {
        if ($programLearningOutcomeMap === []) {
            return;
        }

        $oldLearningOutcomeIds = array_keys($cloneResult->learningOutcomeMap);

        if ($oldLearningOutcomeIds === []) {
            return;
        }

        foreach ($programLearningOutcomeMap as $oldPloId => $newPloId) {
            $outcomeMaps = OutcomeMap::where('pl_outcome_id', $oldPloId)
                ->whereIn('l_outcome_id', $oldLearningOutcomeIds)
                ->get();

            foreach ($outcomeMaps as $outcomeMap) {
                $newCloId = $cloneResult->getClonedLearningOutcomeId($outcomeMap->l_outcome_id);

                if ($newCloId === null) {
                    continue;
                }

                $newOutcomeMap = new OutcomeMap;
                $newOutcomeMap->l_outcome_id = $newCloId;
                $newOutcomeMap->pl_outcome_id = $newPloId;
                $newOutcomeMap->map_scale_id = $mappingScaleMap[$outcomeMap->map_scale_id] ?? $outcomeMap->map_scale_id;
                $newOutcomeMap->save();
            }
        }
    }
}

