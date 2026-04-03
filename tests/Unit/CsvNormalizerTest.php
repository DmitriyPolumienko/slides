<?php

namespace Tests\Unit;

use App\Services\DataIngestion\CsvNormalizer;
use Tests\TestCase;

class CsvNormalizerTest extends TestCase
{
    public function test_normalizes_csv(): void
    {
        $normalizer = new CsvNormalizer();
        $csv = "name,value,category\nAlpha,100,A\nBeta,200,B\nGamma,150,C";

        $result = $normalizer->normalize($csv);

        $this->assertEquals('tabular', $result['type']);
        $this->assertEquals(['name', 'value', 'category'], $result['columns']);
        $this->assertCount(3, $result['rows']);
        $this->assertEquals('Alpha', $result['rows'][0]['name']);
    }

    public function test_empty_csv_returns_empty(): void
    {
        $normalizer = new CsvNormalizer();
        $result = $normalizer->normalize('');
        $this->assertEmpty($result['rows']);
    }
}
