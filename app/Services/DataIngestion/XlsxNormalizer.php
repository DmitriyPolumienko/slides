<?php

namespace App\Services\DataIngestion;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class XlsxNormalizer implements NormalizerInterface
{
    public function supports(string $type): bool
    {
        return in_array($type, ['xlsx', 'xls']);
    }

    public function normalize(mixed $input): array
    {
        if (!file_exists($input)) {
            return ['rows' => [], 'columns' => []];
        }

        try {
            $data = Excel::toArray([], $input);
            $sheet = $data[0] ?? [];
            if (empty($sheet)) {
                return ['rows' => [], 'columns' => []];
            }

            $headers = array_shift($sheet);
            $rows = [];
            foreach ($sheet as $row) {
                $rows[] = array_combine($headers, array_pad($row, count($headers), null));
            }

            return ['type' => 'tabular', 'columns' => $headers, 'rows' => $rows, 'count' => count($rows)];
        } catch (\Exception $e) {
            Log::error('XLSX normalization failed: ' . $e->getMessage());
            return ['rows' => [], 'columns' => [], 'error' => $e->getMessage()];
        }
    }
}
