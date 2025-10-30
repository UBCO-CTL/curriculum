<?php

namespace App\Jobs;

use App\Models\CourseCloneRequest;
use App\Models\MappingScale;
use App\Models\ProgramLearningOutcome;
use App\Services\CourseCloneService;
use App\Services\ProgramMappingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessCourseClone implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly int $courseCloneRequestId)
    {
        $this->onQueue('course-clones');
    }

    /**
     * Execute the job.
     */
    public function handle(CourseCloneService $courseCloneService, ProgramMappingService $programMappingService): void
    {
        try {
            DB::transaction(function () use ($courseCloneService, $programMappingService) {
                $cloneRequest = CourseCloneRequest::with(['program', 'originalCourse', 'requestedBy'])
                    ->lockForUpdate()
                    ->find($this->courseCloneRequestId);

                if ($cloneRequest === null) {
                    return;
                }

                if ($cloneRequest->status !== CourseCloneRequest::STATUS_APPROVED) {
                    return;
                }

                if ($cloneRequest->cloned_course_id !== null) {
                    return;
                }

                $program = $cloneRequest->program;
                $originalCourse = $cloneRequest->originalCourse;
                $requestedBy = $cloneRequest->requestedBy;

                if ($program === null || $originalCourse === null || $requestedBy === null) {
                    $cloneRequest->markFailed();

                    return;
                }

                $cloneResult = $courseCloneService->cloneCourse($originalCourse, [], $requestedBy);

                $programMappingService->attachCourseToProgram($program, $cloneResult->clone->course_id, [
                    'course_required' => $cloneRequest->course_required,
                    'instructor_assigned' => $cloneRequest->instructor_assigned,
                    'map_status' => $cloneRequest->map_status ?? 0,
                    'note' => $cloneRequest->note,
                ]);

                $programLearningOutcomeMap = $this->buildProgramLearningOutcomeMap($cloneRequest);
                $mappingScaleMap = $this->buildMappingScaleMap($cloneRequest);

                $programMappingService->duplicateOutcomeMapsForCourse(
                    $programLearningOutcomeMap,
                    $mappingScaleMap,
                    $cloneResult
                );

                $cloneRequest->forceFill([
                    'cloned_course_id' => $cloneResult->clone->course_id,
                ])->save();
            }, 3);
        } catch (Throwable $throwable) {
            Log::error('Failed processing course clone request', [
                'course_clone_request_id' => $this->courseCloneRequestId,
                'exception' => $throwable,
            ]);

            $cloneRequest = CourseCloneRequest::find($this->courseCloneRequestId);

            if ($cloneRequest !== null) {
                $cloneRequest->markFailed();
            }

            throw $throwable;
        }
    }

    /**
     * @return array<int,int>
     */
    private function buildProgramLearningOutcomeMap(CourseCloneRequest $cloneRequest): array
    {
        return ProgramLearningOutcome::where('program_id', $cloneRequest->program_id)
            ->whereNotNull('source_pl_outcome_id')
            ->pluck('pl_outcome_id', 'source_pl_outcome_id')
            ->toArray();
    }

    /**
     * @return array<int,int>
     */
    private function buildMappingScaleMap(CourseCloneRequest $cloneRequest): array
    {
        return MappingScale::join('mapping_scale_programs', 'mapping_scales.map_scale_id', '=', 'mapping_scale_programs.map_scale_id')
            ->where('mapping_scale_programs.program_id', $cloneRequest->program_id)
            ->whereNotNull('mapping_scales.source_map_scale_id')
            ->distinct()
            ->pluck('mapping_scales.map_scale_id', 'mapping_scales.source_map_scale_id')
            ->toArray();
    }
}
