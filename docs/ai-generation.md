# AI Generation

## Overview

The AI generation pipeline follows this flow:

```
User prompt + Dataset + Template schema
          ↓
     PromptBuilder
          ↓
   LLMProviderInterface (OpenAI / mock)
          ↓
      Raw JSON response
          ↓
     SchemaValidator
          ↓
   SlideSlot population
```

## Retry Logic

`GenerationService` retries up to 3 times if the response fails schema validation. Each attempt is logged in the `ai_generations` table with status `success`, `invalid_schema`, or `failed`.

## Mock Mode

When `OPENAI_API_KEY` is empty, `OpenAIProvider` returns mock content. This allows development and testing without an API key.

## Adding a New Provider

1. Implement `LLMProviderInterface`:
   ```php
   class AnthropicProvider implements LLMProviderInterface {
       public function getName(): string { return 'anthropic'; }
       public function generate(string $prompt, array $schema): array { ... }
   }
   ```
2. Inject it into `GenerationService` in the Livewire component or via a service container binding.

## Prompt Structure

The prompt includes:
- Language, project, theme context
- Full template schema
- Editable slot definitions with constraints
- Dataset (if provided)
- User instruction
