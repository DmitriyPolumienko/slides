<?php

namespace Tests\Unit;

use App\Services\AI\SchemaValidator;
use Tests\TestCase;

class SchemaValidatorTest extends TestCase
{
    private SchemaValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new SchemaValidator();
    }

    public function test_valid_content_passes(): void
    {
        $schema = [
            'title' => ['type' => 'text', 'max_chars' => 80, 'required' => true],
            'body' => ['type' => 'text', 'max_chars' => 500, 'required' => false],
        ];
        $content = ['slots' => ['title' => 'Hello World', 'body' => 'Some body text']];

        $this->assertTrue($this->validator->isValid($content, $schema));
    }

    public function test_missing_required_slot_fails(): void
    {
        $schema = ['title' => ['type' => 'text', 'max_chars' => 80, 'required' => true]];
        $content = ['slots' => []];

        $this->assertFalse($this->validator->isValid($content, $schema));
    }

    public function test_overflow_fails(): void
    {
        $schema = ['title' => ['type' => 'text', 'max_chars' => 10, 'required' => false]];
        $content = ['slots' => ['title' => 'This is way too long for the slot']];

        $errors = $this->validator->validate($content, $schema);
        $this->assertNotEmpty($errors);
    }

    public function test_chart_missing_keys_fails(): void
    {
        $schema = ['chart' => ['type' => 'chart', 'required' => false]];
        $content = ['slots' => ['chart' => ['type' => 'pie']]];

        $errors = $this->validator->validate($content, $schema);
        $this->assertNotEmpty($errors);
    }
}
