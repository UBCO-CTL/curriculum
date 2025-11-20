<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Services\AI\ProgramComparisonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class ProgramAIController extends Controller
{
    public function __construct(
        private readonly ProgramComparisonService $comparisonService
    ) {
        $this->middleware(['auth', 'verified', 'hasAccess']);
    }

    public function compare(Request $request, Program $program): RedirectResponse
    {
        $this->guardRole($program);

        $validated = $request->validate([
            'comparison_program_id' => [
                'required',
                'integer',
                Rule::exists('programs', 'program_id'),
                Rule::notIn([$program->program_id]),
            ],
            'ai_opt_in' => ['accepted'],
        ]);

        $comparisonProgram = Program::where('program_id', $validated['comparison_program_id'])->firstOrFail();
        $this->guardAccess($comparisonProgram);

        try {
            $report = $this->comparisonService->compare($program, $comparisonProgram);
        } catch (RuntimeException $exception) {
            Log::warning('AI comparison aborted', [
                'program_id' => $program->program_id,
                'comparison_program_id' => $comparisonProgram->program_id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('programWizard.step4', $program->program_id)
                ->withInput()
                ->with('aiComparisonError', 'The AI comparison could not be completed. Please verify your inputs and try again.');        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('programWizard.step4', $program->program_id)
                ->withInput()
                ->with('aiComparisonError', 'We could not generate a comparison right now. Please try again.');
        }

        return redirect()
            ->route('programWizard.step4', $program->program_id)
            ->with('aiComparisonSuccess', 'AI comparison generated successfully.');
    }

    private function guardRole(Program $program): void
    {
        $user = Auth::user();
        $membership = $program->users()->where('users.id', $user->id)->first();

        if (! $membership) {
            abort(403, 'You do not have access to this program.');
        }

        if ((int) $membership->pivot->permission === 3) {
            abort(403, 'View-only collaborators cannot run AI comparisons.');
        }
    }

    private function guardAccess(Program $comparisonProgram): void
    {
        $user = Auth::user();

        if (! $comparisonProgram->users()->where('users.id', $user->id)->exists()) {
            abort(403, 'You do not have access to the selected comparison program.');
        }
    }
}