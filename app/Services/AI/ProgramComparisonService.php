<?php

namespace App\Services\AI;

use App\Models\AiComparisonLog;
use App\Models\Program;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\Usage;
use RuntimeException;
use Throwable;

class ProgramComparisonService
{
    public function __construct(
        private readonly ProgramComparisonFormatter $formatter
    ) {}

    public function compare(Program $reference, Program $comparison): string
    {
        if (blank(config('prism.providers.gemini.api_key'))) {
            throw new RuntimeException('Gemini API key is not configured.');
        }

        $model = config('prism.providers.gemini.model', 'gemini-flash-latest');
        $systemPrompt = $this->systemPrompt();
        $userPrompt = $this->buildPrompt(
            $this->formatter->format($reference),
            $this->formatter->format($comparison)
        );

        try {
            $response = Prism::text()
                ->using(Provider::Gemini, $model)
                ->withSystemPrompt($systemPrompt)
                ->withPrompt($userPrompt)
                ->withMaxTokens(5000)
                ->withProviderOptions([
                    'thinkingBudget' => 1000,
                ])
                ->asText();
        } catch (Throwable $exception) {
            Log::warning('Gemini comparison failed', [
                'program_id' => $reference->program_id,
                'comparison_program_id' => $comparison->program_id,
                'message' => $exception->getMessage(),
            ]);

            throw new RuntimeException('Unable to retrieve an AI comparison right now. Please try again later.', previous: $exception);
        }

        $this->logUsage($reference, $comparison, $response->usage, $model, $response->text);

        return trim($response->text);
    }

    private function buildPrompt(array $reference, array $comparison): string
    {
        $referenceJson = json_encode($reference, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $comparisonJson = json_encode($comparison, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
You will compare two academic programs. Program A is the reference; Program B is the candidate we want to improve.

Program A (Reference):
{$referenceJson}

Program B (Comparison):
{$comparisonJson}

Provide a structured response with these sections:
1. Overview - concise summary of both programs and the biggest deltas.
2. Strength Overlaps - what Program B already matches from Program A.
3. Gaps & Risks - missing PLO coverage, course sequencing issues, or assessment gaps.
4. Improvement Ideas - specific, actionable recommendations to close the gaps (limit to 5).
5. Suggested Next Checks - human follow-up questions or data you would validate manually.

Use plain text bullet lists, avoid repeating raw JSON, and never invent stakeholder names.
PROMPT;
    }

    private function systemPrompt(): string
    {
        return implode("\n", [
            'You are an experienced curriculum designer.',
            'Focus on clarity, evidence-based reasoning, and actionable insights.',
            'Never mention internal IDs or speculate about individuals.',
            'Highlight when data is missing rather than guessing.',
            'Format your entire response using Markdown. Use headers, bullet points, and bold text as appropriate for maximum clarity and structure.',
        ]);
    }

    private function logUsage(Program $reference, Program $comparison, ?Usage $usage, string $model, string $report): void
    {
        if (! $usage) {
            return;
        }

        AiComparisonLog::create([
            'program_id' => $reference->program_id,
            'comparison_program_id' => $comparison->program_id,
            'prompt_tokens' => $usage->promptTokens ?? null,
            'completion_tokens' => $usage->completionTokens ?? null,
            'model' => $model,
            'report_markdown' => $report,
            'comparison_program_name' => $comparison->program,
        ]);
    }
}