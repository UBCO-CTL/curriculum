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
        private readonly ProgramComparisonFormatter $formatter,
        private readonly ProgramComparisonPromptBuilder $promptBuilder
    ) {}

    public function compare(Program $reference, Program $comparison): string
    {
        if (blank(config('prism.providers.gemini.api_key'))) {
            throw new RuntimeException('Gemini API key is not configured.');
        }

        $model = config('prism.providers.gemini.model', 'gemini-flash-latest');
        $systemPrompt = $this->promptBuilder->buildSystemPrompt();
        $userPrompt = $this->promptBuilder->buildUserPrompt(
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