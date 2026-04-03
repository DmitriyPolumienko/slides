<?php

namespace App\Services\AI;

interface LLMProviderInterface
{
    public function generate(string $prompt, array $schema): array;
    public function getName(): string;
}
