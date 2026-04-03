<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIProvider implements LLMProviderInterface
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key', '');
        $this->model = config('services.openai.model', 'gpt-4o');
    }

    public function getName(): string
    {
        return 'openai';
    }

    public function generate(string $prompt, array $schema): array
    {
        if (empty($this->apiKey)) {
            return $this->mockResponse($schema);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $this->buildSystemPrompt($schema)],
                ['role' => 'user', 'content' => $prompt],
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.7,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('OpenAI API error: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content');
        return json_decode($content, true) ?? [];
    }

    private function buildSystemPrompt(array $schema): string
    {
        return "You are a presentation content generator. You MUST return valid JSON only. " .
            "Follow this schema exactly:\n" . json_encode($schema, JSON_PRETTY_PRINT) . "\n" .
            "IMPORTANT: Only fill editable slots. Never modify locked_zones.";
    }

    private function mockResponse(array $schema): array
    {
        $slots = [];
        foreach ($schema['editable_slots'] ?? [] as $key => $config) {
            if ($config['type'] === 'text') {
                $slots[$key] = "Sample {$key} content (AI not configured)";
            } elseif ($config['type'] === 'chart') {
                $slots[$key] = [
                    'type' => 'bar',
                    'labels' => ['Item A', 'Item B', 'Item C'],
                    'values' => [45, 30, 25],
                ];
            }
        }
        return ['slots' => $slots];
    }
}
