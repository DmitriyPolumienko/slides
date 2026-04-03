<?php

namespace App\Services\AI;

use App\Models\AiGeneration;
use App\Models\Slide;
use Illuminate\Support\Facades\Log;

class GenerationService
{
    private const MAX_ATTEMPTS = 3;

    public function __construct(
        private LLMProviderInterface $provider,
        private PromptBuilder $promptBuilder,
        private SchemaValidator $validator
    ) {}

    public function generateForSlide(Slide $slide, array $dataset, string $userPrompt): array
    {
        $template = $slide->presentation->masterTemplate?->activeVersion();
        $slotSchema = $template?->editable_slots ?? [];
        $fullSchema = $template?->schema ?? [];

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            $prompt = $this->promptBuilder->build($slide, $dataset, $userPrompt);
            if ($attempt > 1) {
                $prompt .= "\n\nPREVIOUS ATTEMPT FAILED. Please fix and return valid JSON with correct slot types.";
            }

            try {
                $response = $this->provider->generate($prompt, $fullSchema);
                $errors = $this->validator->validate($response, $slotSchema);
                $status = empty($errors) ? 'success' : 'invalid_schema';

                AiGeneration::create([
                    'slide_id' => $slide->id,
                    'provider' => $this->provider->getName(),
                    'model' => 'gpt-4o',
                    'prompt_sent' => $prompt,
                    'schema_sent' => $fullSchema,
                    'response_raw' => $response,
                    'response_parsed' => $response,
                    'status' => $status,
                    'attempt' => $attempt,
                ]);

                if ($status === 'success') {
                    return $response;
                }

                if ($attempt === self::MAX_ATTEMPTS) {
                    Log::warning('AI generation failed after max attempts', [
                        'slide_id' => $slide->id,
                        'errors' => $errors,
                    ]);
                    return $response;
                }
            } catch (\Exception $e) {
                AiGeneration::create([
                    'slide_id' => $slide->id,
                    'provider' => $this->provider->getName(),
                    'model' => 'gpt-4o',
                    'prompt_sent' => $prompt,
                    'schema_sent' => $fullSchema,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'attempt' => $attempt,
                ]);

                if ($attempt === self::MAX_ATTEMPTS) {
                    throw $e;
                }
            }
        }

        return [];
    }
}
