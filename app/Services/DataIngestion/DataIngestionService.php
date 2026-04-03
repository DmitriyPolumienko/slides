<?php

namespace App\Services\DataIngestion;

use App\Models\DataSource;
use App\Models\Presentation;
use Illuminate\Http\UploadedFile;

class DataIngestionService
{
    private array $normalizers;

    public function __construct()
    {
        $this->normalizers = [
            new CsvNormalizer(),
            new XlsxNormalizer(),
            new TextNormalizer(),
        ];
    }

    public function ingestText(Presentation $presentation, string $text): DataSource
    {
        $normalizer = $this->findNormalizer('text');
        $dataset = $normalizer->normalize($text);

        return DataSource::create([
            'presentation_id' => $presentation->id,
            'source_type' => 'text',
            'raw_content' => $text,
            'dataset_json' => $dataset,
        ]);
    }

    public function ingestCsv(Presentation $presentation, string $csvContent): DataSource
    {
        $normalizer = $this->findNormalizer('csv');
        $dataset = $normalizer->normalize($csvContent);

        return DataSource::create([
            'presentation_id' => $presentation->id,
            'source_type' => 'csv',
            'raw_content' => $csvContent,
            'dataset_json' => $dataset,
        ]);
    }

    public function ingestFile(Presentation $presentation, UploadedFile $file): DataSource
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->store('data_sources', 'local');

        $normalizer = $this->findNormalizer($extension);
        $fullPath = storage_path('app/' . $path);
        $dataset = $normalizer ? $normalizer->normalize($fullPath) : [];

        return DataSource::create([
            'presentation_id' => $presentation->id,
            'source_type' => $extension,
            'file_path' => $path,
            'dataset_json' => $dataset,
        ]);
    }

    public function getMergedDataset(Presentation $presentation): array
    {
        $sources = $presentation->dataSources()->whereNotNull('dataset_json')->get();
        $merged = [];
        foreach ($sources as $source) {
            $merged[] = $source->dataset_json;
        }
        return $merged;
    }

    private function findNormalizer(string $type): NormalizerInterface
    {
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->supports($type)) {
                return $normalizer;
            }
        }
        return new TextNormalizer();
    }
}
