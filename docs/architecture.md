# Architecture Overview

## Stack
- **Laravel 11** — PHP framework
- **Livewire 3** — Reactive server-side components
- **Alpine.js** — Lightweight client-side interactivity
- **Tailwind CSS** — Utility-first CSS
- **SQLite** — Default database (swappable)

## Directory Structure

```
app/
├── Http/Controllers/
│   └── PresentationController.php   # CRUD + export endpoints
├── Livewire/
│   └── Builder.php                  # Main interactive builder component
├── Models/                          # Eloquent models
│   ├── Presentation.php
│   ├── Slide.php
│   ├── SlideSlot.php
│   ├── MasterTemplate.php
│   ├── TemplateVersion.php
│   ├── Project.php
│   ├── Theme.php
│   ├── Language.php
│   ├── DataSource.php
│   └── AiGeneration.php
└── Services/
    ├── AI/                          # AI generation pipeline
    │   ├── LLMProviderInterface.php
    │   ├── OpenAIProvider.php
    │   ├── PromptBuilder.php
    │   ├── SchemaValidator.php
    │   └── GenerationService.php
    ├── DataIngestion/               # Data import pipeline
    │   ├── NormalizerInterface.php
    │   ├── CsvNormalizer.php
    │   ├── XlsxNormalizer.php
    │   ├── TextNormalizer.php
    │   └── DataIngestionService.php
    └── Export/                      # Export pipeline
        ├── PdfExportService.php
        ├── PptxExportService.php
        └── FigmaExportService.php
```

## Data Flow

1. User creates a **Presentation** linked to a **Project**, **Theme**, **Language**, and **MasterTemplate**
2. **MasterTemplate** has **TemplateVersions** defining locked zones (header/footer) and editable slots
3. User adds **Slides** to the presentation; each slide gets **SlideSlots** initialized from the template
4. User can import data via **DataSources** (CSV, XLSX, text)
5. AI **GenerationService** builds a prompt, calls the LLM, validates the response, and populates slots
6. Export services render the presentation to PDF, PPTX, or Figma JSON
