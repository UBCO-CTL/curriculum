<?php

namespace App\Services\AI;

class ProgramComparisonPromptBuilder
{
    public function buildSystemPrompt(): string
    {
        return implode("\n", [
            'You are an experienced curriculum designer.',
            'Focus on clarity, evidence-based reasoning, and actionable insights.',
            'Never mention internal IDs or speculate about individuals.',
            'Highlight when data is missing rather than guessing.',
            'Format your entire response using Markdown. Use headers, bullet points, and bold text as appropriate for maximum clarity and structure.',
        ]);
    }

    public function buildUserPrompt(array $reference, array $comparison): string
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
}

