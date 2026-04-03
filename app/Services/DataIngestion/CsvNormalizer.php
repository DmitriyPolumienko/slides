<?php

namespace App\Services\DataIngestion;

class CsvNormalizer implements NormalizerInterface
{
    public function supports(string $type): bool
    {
        return $type === 'csv';
    }

    public function normalize(mixed $input): array
    {
        $lines = is_string($input) ? explode("\n", trim($input)) : [];
        if (empty($lines)) {
            return ['rows' => [], 'columns' => []];
        }

        $headers = str_getcsv(array_shift($lines));
        $rows = [];
        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }
            $values = str_getcsv($line);
            $rows[] = array_combine($headers, array_pad($values, count($headers), null));
        }

        return ['type' => 'tabular', 'columns' => $headers, 'rows' => $rows, 'count' => count($rows)];
    }
}
