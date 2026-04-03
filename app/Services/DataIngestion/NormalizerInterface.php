<?php

namespace App\Services\DataIngestion;

interface NormalizerInterface
{
    public function normalize(mixed $input): array;
    public function supports(string $type): bool;
}
