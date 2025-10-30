<?php

namespace App\Services;

use App\Models\Course;

class CourseCloneResult
{
    /** @var array<int,int> */
    public readonly array $learningOutcomeMap;

    /** @var array<int,int> */
    public readonly array $assessmentMethodMap;

    /** @var array<int,int> */
    public readonly array $learningActivityMap;

    public function __construct(
        public readonly Course $source,
        public readonly Course $clone,
        array $learningOutcomeMap,
        array $assessmentMethodMap,
        array $learningActivityMap
    ) {
        $this->learningOutcomeMap = $learningOutcomeMap;
        $this->assessmentMethodMap = $assessmentMethodMap;
        $this->learningActivityMap = $learningActivityMap;
    }

    public function getClonedLearningOutcomeId(int $originalId): ?int
    {
        return $this->learningOutcomeMap[$originalId] ?? null;
    }

    public function getClonedAssessmentMethodId(int $originalId): ?int
    {
        return $this->assessmentMethodMap[$originalId] ?? null;
    }

    public function getClonedLearningActivityId(int $originalId): ?int
    {
        return $this->learningActivityMap[$originalId] ?? null;
    }
}

