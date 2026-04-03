<?php

namespace App\Services\AI;

class SchemaValidator
{
    public function validate(array $generatedContent, array $slotSchema): array
    {
        $errors = [];
        $slots = $generatedContent['slots'] ?? [];

        foreach ($slotSchema as $key => $config) {
            if (($config['required'] ?? false) && empty($slots[$key])) {
                $errors[] = "Required slot '{$key}' is missing.";
                continue;
            }

            if (!isset($slots[$key])) {
                continue;
            }

            $value = $slots[$key];

            if ($config['type'] === 'text') {
                $maxChars = $config['max_chars'] ?? 500;
                if (is_string($value) && mb_strlen($value) > $maxChars) {
                    $errors[] = "Slot '{$key}' exceeds max chars ({$maxChars}). Got: " . mb_strlen($value);
                }
            }

            if ($config['type'] === 'chart') {
                if (!is_array($value)) {
                    $errors[] = "Slot '{$key}' must be a chart object (array).";
                } elseif (!isset($value['type'], $value['labels'], $value['values'])) {
                    $errors[] = "Chart slot '{$key}' missing required keys: type, labels, values.";
                }
            }
        }

        return $errors;
    }

    public function isValid(array $generatedContent, array $slotSchema): bool
    {
        return empty($this->validate($generatedContent, $slotSchema));
    }
}
