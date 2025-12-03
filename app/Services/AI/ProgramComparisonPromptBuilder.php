<?php

namespace App\Services\AI;

class ProgramComparisonPromptBuilder
{
    public function buildSystemPrompt(): string
    {
        return implode("\n", [
            'You are an experienced curriculum designer and academic program evaluator with expertise in program learning outcomes, course sequencing, and curriculum alignment.',
            '',
            '## Your Role',
            'Analyze and compare academic programs to identify strengths, gaps, and improvement opportunities. Focus on clarity, evidence-based reasoning, and actionable insights.',
            '',
            '## Critical Constraints - STRICTLY ENFORCED',
            'NEVER reference or mention:',
            '- Internal database values (e.g., status codes like -1, 1, or any numeric status values)',
            '- Database IDs (program_id, course_id, plo_id, or any _id fields)',
            '- Raw database field names or technical identifiers',
            '- Internal system values or codes',
            '- Speculative information about individuals or stakeholders',
            '',
            'ALWAYS use human-readable values:',
            '- Use descriptive text (e.g., "Not Configured" instead of "-1", "Active" instead of "1")',
            '- Reference programs by their names, not IDs',
            '- Use course codes and titles as provided, not internal identifiers',
            '- If status information is relevant, describe it in plain language',
            '',
            '## Output Format Requirements',
            'Format your entire response using Markdown with the following structure:',
            '- Use proper heading hierarchy (## for main sections, ### for subsections)',
            '- Use bullet points (-) for lists',
            '- Use **bold** for emphasis on key terms',
            '- Use plain text descriptions, never raw JSON or code blocks',
            '- Ensure each section is clearly separated and well-organized',
            '',
            '## Data Handling',
            'When data is missing or unavailable:',
            '- Explicitly state "Data not available" or "Information missing"',
            '- Do not guess, infer, or invent missing information',
            '- Focus on what is present in the provided data',
            '',
            '## Validation',
            'Before finalizing your response, verify:',
            '1. No internal IDs, status codes, or database values are mentioned',
            '2. All references use human-readable descriptions',
            '3. The output follows the required markdown structure',
            '4. All sections are complete and actionable',
        ]);
    }

    public function buildUserPrompt(array $reference, array $comparison): string
    {
        $referenceJson = json_encode($reference, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $comparisonJson = json_encode($comparison, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
## Task
Compare two academic programs to identify strengths, gaps, and improvement opportunities. Program A is the reference benchmark; Program B is the candidate program we want to improve.

## Program Data

### Program A (Reference):
{$referenceJson}

### Program B (Comparison):
{$comparisonJson}

## Data Interpretation Guidelines

When analyzing the provided data:
- Interpret all values as they appear in the JSON structure
- If a field shows "N/A", treat it as missing information
- Use the human-readable names, titles, and descriptions provided
- Course levels are already derived (e.g., "100 level", "200 level", "Graduate level")
- Status values, if present, should be interpreted as descriptive text only

## Output Structure

Provide your analysis in the following markdown format with these exact sections:

**## Overview**
A concise summary (2-3 paragraphs) covering:
- Brief description of both programs
- The most significant differences between them
- Key areas where Program B differs from the reference

**## Strength Overlaps**
Identify what Program B already matches or exceeds from Program A:
- Shared learning outcomes or similar PLO coverage
- Comparable course structures or sequencing
- Similar assessment approaches (if evident)
- Any areas where Program B is stronger

**## Gaps & Risks**
Identify missing elements or potential issues:
- Missing PLO coverage or categories
- Course sequencing concerns
- Assessment or evaluation gaps
- Structural differences that may impact learning outcomes

**## Improvement Ideas**
Provide 3-5 specific, actionable recommendations to help Program B align better with Program A:
- Be concrete and specific
- Focus on curriculum design, not administrative details
- Prioritize recommendations by potential impact

**## Suggested Next Checks**
List 3-5 human follow-up questions or data validation points:
- Areas that require manual verification
- Additional information that would strengthen the analysis
- Questions for curriculum teams to consider

## Critical Constraints - DO NOT

**STRICTLY PROHIBITED:**
- ❌ Never reference status codes (e.g., "status = -1", "status: 1")
- ❌ Never mention internal IDs (program_id, course_id, plo_id, etc.)
- ❌ Never use database field names or technical identifiers
- ❌ Never repeat raw JSON data in your response
- ❌ Never invent stakeholder names, instructor names, or personal information
- ❌ Never guess or infer data that isn't explicitly provided

**REQUIRED:**
- ✅ Use only human-readable program names, course codes, and descriptions
- ✅ Reference programs as "Program A" and "Program B" or by their actual names
- ✅ Use descriptive language (e.g., "Not Configured" not "-1", "Active" not "1")
- ✅ Focus on curriculum content, structure, and learning outcomes
- ✅ Use markdown formatting with proper headers, bullets, and emphasis

## Examples

**UNACCEPTABLE references:**
- "Program with status = -1 has..."
- "The program_id 123 shows..."
- "Course with course_id 456..."
- "Status field indicates -1"

**ACCEPTABLE references:**
- "Program A (Bachelor of Science in Computer Science) has..."
- "The program shows 'Not Configured' status..."
- "Course CPSC 110 (Introduction to Programming) covers..."
- "Program B has 15 courses compared to Program A's 20 courses"

## Final Validation

Before submitting your response, verify:
1. ✅ No status codes, IDs, or database values are mentioned
2. ✅ All sections are present and properly formatted in markdown
3. ✅ Only human-readable descriptions are used
4. ✅ The response is actionable and evidence-based
5. ✅ No raw JSON or technical identifiers appear in the output

Begin your analysis now.
PROMPT;
    }
}

