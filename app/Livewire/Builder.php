<?php

namespace App\Livewire;

use App\Models\Presentation;
use App\Models\Slide;
use App\Models\SlideSlot;
use App\Services\AI\GenerationService;
use App\Services\AI\OpenAIProvider;
use App\Services\AI\PromptBuilder;
use App\Services\AI\SchemaValidator;
use App\Services\DataIngestion\DataIngestionService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class Builder extends Component
{
    use WithFileUploads;

    public Presentation $presentation;
    public int $activeSlideId = 0;
    public string $rawTextInput = '';
    public $uploadedFile = null;

    public array $headerOptions = [
        'show_logo' => true,
        'show_project_name' => true,
        'show_date' => false,
    ];

    public array $footerOptions = [
        'show_page_number' => true,
        'show_confidentiality' => false,
    ];

    public function mount(Presentation $presentation): void
    {
        $this->presentation = $presentation->load([
            'slides.slots',
            'dataSources',
            'project',
            'theme',
            'language',
            'masterTemplate.versions',
        ]);
        $this->headerOptions = $presentation->header_options ?? $this->headerOptions;
        $this->footerOptions = $presentation->footer_options ?? $this->footerOptions;

        if ($presentation->slides->isNotEmpty()) {
            $this->activeSlideId = $presentation->slides->first()->id;
        }
    }

    public function addSlide(string $slideType = 'text_bullets'): void
    {
        $order = $this->presentation->slides()->count() + 1;
        $slide = $this->presentation->slides()->create([
            'order' => $order,
            'slide_type' => $slideType,
            'user_prompt' => '',
        ]);

        $this->initializeSlots($slide);
        $this->activeSlideId = $slide->id;
        $this->presentation->refresh();
        $this->presentation->load('slides.slots');
    }

    private function initializeSlots(Slide $slide): void
    {
        $template = $this->presentation->masterTemplate?->activeVersion();
        if (!$template) {
            return;
        }

        foreach ($template->editable_slots as $key => $config) {
            SlideSlot::create([
                'slide_id' => $slide->id,
                'slot_key' => $key,
                'slot_type' => $config['type'],
                'content' => '',
            ]);
        }
    }

    public function selectSlide(int $slideId): void
    {
        $this->activeSlideId = $slideId;
    }

    public function deleteSlide(int $slideId): void
    {
        $slide = Slide::find($slideId);
        if ($slide && !$slide->is_locked) {
            $slide->delete();
            $this->presentation->refresh();
            $this->presentation->load('slides.slots');
            $this->activeSlideId = $this->presentation->slides->first()?->id ?? 0;
        }
    }

    public function lockSlide(int $slideId): void
    {
        Slide::find($slideId)?->update(['is_locked' => true]);
        $this->presentation->refresh();
        $this->presentation->load('slides.slots');
    }

    public function generateContent(int $slideId): void
    {
        $slide = Slide::with(['slots', 'presentation'])->find($slideId);
        if (!$slide || $slide->is_locked) {
            $this->dispatch('notify', message: 'Slide is locked or not found.', type: 'error');
            return;
        }

        try {
            $service = new GenerationService(
                new OpenAIProvider(),
                new PromptBuilder(),
                new SchemaValidator()
            );

            $ingestionService = new DataIngestionService();
            $dataset = $ingestionService->getMergedDataset($this->presentation);

            $result = $service->generateForSlide($slide, $dataset, $slide->user_prompt ?? '');

            if (!empty($result['slots'])) {
                foreach ($result['slots'] as $key => $value) {
                    $slot = $slide->slots()->where('slot_key', $key)->first();
                    if ($slot && !$slot->is_locked) {
                        $slot->update(['content' => is_array($value) ? json_encode($value) : $value]);
                    }
                }
            }

            $this->presentation->refresh();
            $this->presentation->load('slides.slots');
            $this->dispatch('notify', message: 'Content generated successfully!', type: 'success');
        } catch (\Exception $e) {
            Log::error('Generation failed: ' . $e->getMessage());
            $this->dispatch('notify', message: 'Generation failed: ' . $e->getMessage(), type: 'error');
        }
    }

    public function regenerateSlot(int $slideId, string $slotKey): void
    {
        $slide = Slide::with(['slots', 'presentation.masterTemplate.versions'])->find($slideId);
        if (!$slide || $slide->is_locked) {
            return;
        }

        $slot = $slide->slots()->where('slot_key', $slotKey)->where('is_locked', false)->first();
        if (!$slot) {
            return;
        }

        try {
            $service = new GenerationService(
                new OpenAIProvider(),
                new PromptBuilder(),
                new SchemaValidator()
            );

            $ingestionService = new DataIngestionService();
            $dataset = $ingestionService->getMergedDataset($this->presentation);

            $prompt = "Regenerate only the '{$slotKey}' slot. " . ($slide->user_prompt ?? '');
            $result = $service->generateForSlide($slide, $dataset, $prompt);

            if (isset($result['slots'][$slotKey])) {
                $value = $result['slots'][$slotKey];
                $slot->update(['content' => is_array($value) ? json_encode($value) : $value]);
            }

            $this->presentation->refresh();
            $this->presentation->load('slides.slots');
            $this->dispatch('notify', message: "Slot '{$slotKey}' regenerated.", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Regeneration failed.', type: 'error');
        }
    }

    public function updateHeaderOptions(): void
    {
        $this->presentation->update(['header_options' => $this->headerOptions]);
    }

    public function updateFooterOptions(): void
    {
        $this->presentation->update(['footer_options' => $this->footerOptions]);
    }

    public function ingestText(): void
    {
        if (empty($this->rawTextInput)) {
            return;
        }

        $service = new DataIngestionService();
        $service->ingestText($this->presentation, $this->rawTextInput);
        $this->rawTextInput = '';
        $this->presentation->refresh();
        $this->presentation->load('dataSources');
        $this->dispatch('notify', message: 'Text data added.', type: 'success');
    }

    public function ingestFile(): void
    {
        if (!$this->uploadedFile) {
            return;
        }

        $service = new DataIngestionService();
        $service->ingestFile($this->presentation, $this->uploadedFile);
        $this->uploadedFile = null;
        $this->presentation->refresh();
        $this->presentation->load('dataSources');
        $this->dispatch('notify', message: 'File imported.', type: 'success');
    }

    public function updateSlotContent(int $slotId, string $content): void
    {
        $slot = SlideSlot::find($slotId);
        if ($slot && !$slot->is_locked) {
            $slot->update(['content' => $content]);
        }
    }

    public function updateSlidePrompt(int $slideId, string $prompt): void
    {
        Slide::find($slideId)?->update(['user_prompt' => $prompt]);
    }

    public function render()
    {
        return view('livewire.builder', [
            'activeSlide' => $this->activeSlideId
                ? Slide::with('slots')->find($this->activeSlideId)
                : null,
        ]);
    }
}
