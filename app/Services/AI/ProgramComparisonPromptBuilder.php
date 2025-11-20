<?php

namespace App\Services\AI;

class ProgramComparisonPromptBuilder
{
    public function buildSystemPrompt(): string
    {
        return <<<PROMPT
You are an impartial expert in curriculum design. Compare two academic programs to:
- highlight overlapping strengths,
- identify gaps or areas needing development in the comparison program versus the reference program,
- suggest concrete improvements (new PLO emphases, course adjustments, sequencing, or assessment refinements).

Ground your analysis strictly in the provided data; do not invent institutional facts or reference individuals.
PROMPT;
    }

    public function buildUserPrompt(array $referenceProgram, array $comparisonProgram): string
    {
        $payload = [
            'reference_program' => $referenceProgram,
            'comparison_program' => $comparisonProgram,
        ];

        $instructions = <<<PROMPT
Using the structured data below, compare `comparison_program` against `reference_program`.
Respond with three markdown sections titled:
1. Alignment & Shared Strengths
2. Gaps or Risks
3. Recommended Improvements

Each section should use concise bullet points that cite specific PLOs, course tiers, or alignment counts when possible.
PROMPT;

        return $instructions . "\n\nDATA:\n" . json_encode(
            $payload,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}

