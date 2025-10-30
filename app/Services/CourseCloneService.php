<?php

namespace App\Services;

use App\Models\AssessmentMethod;
use App\Models\Course;
use App\Models\CourseOptionalPriorities;
use App\Models\CourseUser;
use App\Models\LearningActivity;
use App\Models\LearningOutcome;
use App\Models\OutcomeActivity;
use App\Models\OutcomeAssessment;
use App\Models\StandardsOutcomeMap;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseCloneService
{
    /**
     * Deep clone a course and its dependent records.
     *
     * @param  array<string,mixed>  $overrides
     */
    public function cloneCourse(Course $source, array $overrides = [], ?User $owner = null): CourseCloneResult
    {
        return DB::transaction(function () use ($source, $overrides, $owner) {
            $clone = $this->createCourseClone($source, $overrides);

            [$learningOutcomeMap, $assessmentMethodMap, $learningActivityMap] = $this->duplicateStructure($source, $clone);

            if ($owner !== null) {
                CourseUser::updateOrCreate(
                    ['course_id' => $clone->course_id, 'user_id' => $owner->id],
                    ['permission' => 1]
                );
            }

            return new CourseCloneResult(
                source: $source,
                clone: $clone->refresh(),
                learningOutcomeMap: $learningOutcomeMap,
                assessmentMethodMap: $assessmentMethodMap,
                learningActivityMap: $learningActivityMap
            );
        });
    }

    /**
     * @param  array<string,mixed>  $overrides
     */
    private function createCourseClone(Course $source, array $overrides): Course
    {
        $clone = new Course;

        $clone->course_code = $this->normalizeCourseCode($overrides['course_code'] ?? $source->course_code);
        $clone->course_num = $this->normalizeCourseNumber($overrides['course_num'] ?? $source->course_num);
        $clone->course_title = $overrides['course_title'] ?? $source->course_title;
        $clone->section = $overrides['section'] ?? $source->section;
        $clone->status = $overrides['status'] ?? ($source->status ?? -1);
        $clone->required = $overrides['required'] ?? $source->required;
        $clone->type = $overrides['type'] ?? $source->type;
        $clone->delivery_modality = $overrides['delivery_modality'] ?? $source->delivery_modality;
        $clone->year = $overrides['year'] ?? $source->year;
        $clone->semester = $overrides['semester'] ?? $source->semester;
        $clone->standard_category_id = $overrides['standard_category_id'] ?? $source->standard_category_id;
        $clone->scale_category_id = $overrides['scale_category_id'] ?? $source->scale_category_id;
        $clone->assigned = $overrides['assigned'] ?? ($source->assigned ?? 1);
        $clone->source_course_id = $source->source_course_id ?? $source->course_id;

        $clone->save();

        return $clone;
    }

    /**
     * @return array{0: array<int,int>, 1: array<int,int>, 2: array<int,int>}
     */
    private function duplicateStructure(Course $source, Course $clone): array
    {
        $source->loadMissing([
            'assessmentMethods',
            'learningActivities',
            'learningOutcomes.learningActivities',
            'learningOutcomes.assessmentMethods',
            'optionalPriorities',
        ]);

        $assessmentMethodMap = [];
        foreach ($source->assessmentMethods as $assessmentMethod) {
            /** @var AssessmentMethod $duplicate */
            $duplicate = $assessmentMethod->replicate();
            $duplicate->course_id = $clone->course_id;
            $duplicate->save();

            $assessmentMethodMap[$assessmentMethod->a_method_id] = $duplicate->a_method_id;
        }

        $learningActivityMap = [];
        foreach ($source->learningActivities as $learningActivity) {
            /** @var LearningActivity $duplicate */
            $duplicate = $learningActivity->replicate();
            $duplicate->course_id = $clone->course_id;
            $duplicate->save();

            $learningActivityMap[$learningActivity->l_activity_id] = $duplicate->l_activity_id;
        }

        $learningOutcomeMap = [];
        foreach ($source->learningOutcomes as $learningOutcome) {
            /** @var LearningOutcome $duplicate */
            $duplicate = $learningOutcome->replicate();
            $duplicate->course_id = $clone->course_id;
            $duplicate->save();

            $learningOutcomeMap[$learningOutcome->l_outcome_id] = $duplicate->l_outcome_id;

            // Outcome Activities
            foreach ($learningOutcome->learningActivities as $activity) {
                $newActivityId = $learningActivityMap[$activity->l_activity_id] ?? null;
                if ($newActivityId === null) {
                    continue;
                }

                $outcomeActivity = new OutcomeActivity;
                $outcomeActivity->l_outcome_id = $duplicate->l_outcome_id;
                $outcomeActivity->l_activity_id = $newActivityId;
                $outcomeActivity->save();
            }

            // Outcome Assessments
            foreach ($learningOutcome->assessmentMethods as $assessmentMethod) {
                $newAssessmentId = $assessmentMethodMap[$assessmentMethod->a_method_id] ?? null;
                if ($newAssessmentId === null) {
                    continue;
                }

                $outcomeAssessment = new OutcomeAssessment;
                $outcomeAssessment->l_outcome_id = $duplicate->l_outcome_id;
                $outcomeAssessment->a_method_id = $newAssessmentId;
                $outcomeAssessment->save();
            }
        }

        // Standards outcome mappings
        $standardOutcomeMaps = StandardsOutcomeMap::where('course_id', $source->course_id)->get();
        foreach ($standardOutcomeMaps as $standardOutcomeMap) {
            $duplicate = $standardOutcomeMap->replicate();
            $duplicate->course_id = $clone->course_id;
            $duplicate->save();
        }

        // Optional priorities pivot
        foreach ($source->optionalPriorities as $priority) {
            $duplicate = new CourseOptionalPriorities;
            $duplicate->op_id = $priority->op_id;
            $duplicate->course_id = $clone->course_id;
            $duplicate->save();
        }

        return [$learningOutcomeMap, $assessmentMethodMap, $learningActivityMap];
    }

    private function normalizeCourseCode(?string $courseCode): ?string
    {
        if ($courseCode === null) {
            return null;
        }

        return Str::upper($courseCode);
    }

    private function normalizeCourseNumber(?string $courseNumber): ?string
    {
        if ($courseNumber === null) {
            return null;
        }

        $trimmed = ltrim($courseNumber, '0');

        return $trimmed === '' ? '0' : $trimmed;
    }
}

