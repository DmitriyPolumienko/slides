<?php

namespace Tests\Unit;

use App\Services\DataIngestion\TextNormalizer;
use Tests\TestCase;

class TextNormalizerTest extends TestCase
{
    public function test_normalizes_text(): void
    {
        $normalizer = new TextNormalizer();
        $result = $normalizer->normalize('Hello world, this is test data.');

        $this->assertEquals('text', $result['type']);
        $this->assertArrayHasKey('content', $result);
        $this->assertArrayHasKey('word_count', $result);
        $this->assertArrayHasKey('char_count', $result);
        $this->assertEquals('Hello world, this is test data.', $result['content']);
    }
}
