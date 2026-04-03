<?php

namespace App\Services\DataIngestion;

class TextNormalizer implements NormalizerInterface
{
    public function supports(string $type): bool
    {
        return $type === 'text';
    }

    public function normalize(mixed $input): array
    {
        $text = is_string($input) ? trim($input) : '';
        return [
            'type' => 'text',
            'content' => $text,
            'word_count' => str_word_count($text),
            'char_count' => mb_strlen($text),
        ];
    }
}
