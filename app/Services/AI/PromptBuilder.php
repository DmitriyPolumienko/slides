<?php

namespace App\Services\AI;

use App\Models\Slide;

class PromptBuilder
{
    public function build(Slide $slide, array $dataset, string $userPrompt): string
    {
        $presentation = $slide->presentation;
        $template = $presentation->masterTemplate?->activeVersion();
        $language = $presentation->language?->name ?? 'English';
        $project = $presentation->project?->name ?? 'Default Project';
        $theme = $presentation->theme?->name ?? 'Default Theme';

        $parts = [];
        $parts[] = "CONTEXT:";
        $parts[] = "- Language: {$language}";
        $parts[] = "- Project: {$project}";
        $parts[] = "- Theme: {$theme}";
        $parts[] = "- Slide type: {$slide->slide_type}";

        if ($template) {
            $parts[] = "\nTEMPLATE SCHEMA (locked zones must NOT be modified):";
            $parts[] = json_encode($template->schema, JSON_PRETTY_PRINT);
            $parts[] = "\nEDITABLE SLOTS (only fill these):";
            $parts[] = json_encode($template->editable_slots, JSON_PRETTY_PRINT);
        }

        if (!empty($dataset)) {
            $parts[] = "\nDATA PROVIDED:";
            $parts[] = json_encode($dataset, JSON_PRETTY_PRINT);
        }

        $parts[] = "\nUSER INSTRUCTION: {$userPrompt}";
        $parts[] = "\nReturn a JSON object with a 'slots' key containing only the editable slot values.";
        $parts[] = "Respect character limits. Write in {$language}.";

        return implode("\n", $parts);
    }
}
