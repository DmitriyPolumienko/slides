<div class="flex flex-col" style="height: calc(100vh - 140px);" x-data="{ activeTab: 'slides' }">
    <!-- Header bar -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-3 flex items-center justify-between rounded-t-lg">
        <div class="flex items-center gap-4">
            <h2 class="font-semibold text-lg">{{ $this->presentation->title }}</h2>
            <span class="text-sm text-gray-500 capitalize">{{ $this->presentation->status }}</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('presentations.export.pdf', $this->presentation) }}" class="text-sm bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded hover:bg-gray-200">PDF</a>
            <a href="{{ route('presentations.export.pptx', $this->presentation) }}" class="text-sm bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded hover:bg-gray-200">PPTX</a>
            <a href="{{ route('presentations.export.figma', $this->presentation) }}" class="text-sm bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded hover:bg-gray-200">Figma JSON</a>
        </div>
    </div>

    <div class="flex flex-1 overflow-hidden border border-gray-200 dark:border-gray-700 rounded-b-lg">
        <!-- Left Panel -->
        <div class="w-80 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden flex-shrink-0">
            <!-- Tabs -->
            <div class="flex border-b border-gray-200 dark:border-gray-700">
                <button @click="activeTab = 'slides'" :class="activeTab === 'slides' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500'" class="flex-1 px-4 py-3 text-sm font-medium">Slides</button>
                <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500'" class="flex-1 px-4 py-3 text-sm font-medium">Settings</button>
                <button @click="activeTab = 'data'" :class="activeTab === 'data' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500'" class="flex-1 px-4 py-3 text-sm font-medium">Data</button>
            </div>

            <!-- Slides Tab -->
            <div x-show="activeTab === 'slides'" class="flex-1 overflow-y-auto p-4 space-y-3">
                @foreach($this->presentation->slides as $slide)
                <div
                    wire:click="selectSlide({{ $slide->id }})"
                    class="border rounded-lg p-3 cursor-pointer transition {{ $activeSlideId === $slide->id ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300' }}"
                >
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium">Slide {{ $loop->iteration }}</span>
                        <div class="flex gap-1">
                            @if($slide->is_locked)
                                <span class="text-xs bg-yellow-100 text-yellow-800 px-1 rounded">🔒</span>
                            @else
                                <button wire:click.stop="lockSlide({{ $slide->id }})" class="text-xs text-gray-400 hover:text-yellow-500" title="Lock slide">🔓</button>
                                <button wire:click.stop="deleteSlide({{ $slide->id }})" class="text-xs text-gray-400 hover:text-red-500" title="Delete">✕</button>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $slide->slide_type) }}</p>

                    @if(!$slide->is_locked)
                    <div class="mt-2" wire:click.stop>
                        <textarea
                            class="w-full text-xs border border-gray-200 dark:border-gray-600 rounded p-2 bg-gray-50 dark:bg-gray-700 resize-none"
                            placeholder="Prompt for this slide..."
                            rows="2"
                            wire:change="updateSlidePrompt({{ $slide->id }}, $event.target.value)"
                        >{{ $slide->user_prompt }}</textarea>
                        <button
                            wire:click.stop="generateContent({{ $slide->id }})"
                            class="w-full mt-1 text-xs bg-indigo-600 text-white py-1 rounded hover:bg-indigo-700"
                        >
                            ✨ Generate
                        </button>
                    </div>
                    @endif
                </div>
                @endforeach

                <!-- Add slide buttons -->
                <div class="pt-2 space-y-1">
                    <p class="text-xs text-gray-500 font-medium">Add Slide:</p>
                    @foreach(['text_bullets', 'chart_insight', 'title_slide', 'comparison', 'media_grid'] as $type)
                    <button wire:click="addSlide('{{ $type }}')" class="w-full text-xs text-left px-3 py-2 border border-dashed border-gray-300 dark:border-gray-600 rounded hover:border-indigo-400 hover:text-indigo-600">
                        + {{ str_replace('_', ' ', ucwords($type, '_')) }}
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Settings Tab -->
            <div x-show="activeTab === 'settings'" class="flex-1 overflow-y-auto p-4 space-y-4">
                <div>
                    <h3 class="font-medium text-sm mb-3">Header Options</h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model.live="headerOptions.show_logo" wire:change="updateHeaderOptions" class="rounded">
                            Show Logo
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model.live="headerOptions.show_project_name" wire:change="updateHeaderOptions" class="rounded">
                            Show Project Name
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model.live="headerOptions.show_date" wire:change="updateHeaderOptions" class="rounded">
                            Show Date
                        </label>
                    </div>
                </div>
                <div>
                    <h3 class="font-medium text-sm mb-3">Footer Options</h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model.live="footerOptions.show_page_number" wire:change="updateFooterOptions" class="rounded">
                            Show Page Number
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model.live="footerOptions.show_confidentiality" wire:change="updateFooterOptions" class="rounded">
                            Confidentiality Label
                        </label>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h3 class="font-medium text-sm mb-2">Presentation Info</h3>
                    <div class="text-sm text-gray-500 space-y-1">
                        <p>Language: {{ $this->presentation->language?->name ?? '—' }}</p>
                        <p>Project: {{ $this->presentation->project?->name ?? '—' }}</p>
                        <p>Theme: {{ $this->presentation->theme?->name ?? '—' }}</p>
                        <p>Template: {{ $this->presentation->masterTemplate?->name ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Tab -->
            <div x-show="activeTab === 'data'" class="flex-1 overflow-y-auto p-4 space-y-4">
                <div>
                    <h3 class="font-medium text-sm mb-2">Import Data</h3>

                    <div class="mb-3">
                        <label class="text-xs text-gray-500 block mb-1">Paste text or CSV data:</label>
                        <textarea
                            wire:model="rawTextInput"
                            class="w-full text-xs border border-gray-200 dark:border-gray-600 rounded p-2 bg-gray-50 dark:bg-gray-700 resize-none"
                            rows="4"
                            placeholder="Paste CSV data or text here..."
                        ></textarea>
                        <button wire:click="ingestText" class="w-full mt-1 text-xs bg-green-600 text-white py-1 rounded hover:bg-green-700">
                            Add Text Data
                        </button>
                    </div>

                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Or upload CSV/XLSX:</label>
                        <input type="file" wire:model="uploadedFile" accept=".csv,.xlsx,.xls" class="text-xs w-full">
                        @if($uploadedFile)
                        <button wire:click="ingestFile" class="w-full mt-1 text-xs bg-blue-600 text-white py-1 rounded hover:bg-blue-700">
                            Import File
                        </button>
                        @endif
                    </div>
                </div>

                @if($this->presentation->dataSources->isNotEmpty())
                <div class="border-t pt-3">
                    <h3 class="font-medium text-sm mb-2">Attached Data Sources</h3>
                    @foreach($this->presentation->dataSources as $source)
                    <div class="text-xs bg-gray-50 dark:bg-gray-700 rounded p-2 mb-1">
                        <span class="uppercase font-medium text-indigo-600">{{ $source->source_type }}</span>
                        @if($source->source_type === 'text')
                            <p class="text-gray-500 truncate">{{ Str::limit($source->raw_content, 50) }}</p>
                        @else
                            <p class="text-gray-500">{{ basename($source->file_path ?? 'file') }}</p>
                        @endif
                        <p class="text-gray-400">{{ $source->dataset_json['count'] ?? $source->dataset_json['char_count'] ?? '?' }} records</p>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Center/Right: Slide Preview -->
        <div class="flex-1 bg-gray-200 dark:bg-gray-900 flex flex-col overflow-hidden">
            @if($activeSlide)
            <div class="flex-1 flex items-center justify-center p-8 overflow-auto">
                @php
                    $template = $this->presentation->masterTemplate?->activeVersion();
                    $lockedZones = $template?->locked_zones ?? [];
                    $theme = $this->presentation->theme;
                    $bgColor = $template?->schema['background'] ?? ($theme?->color_primary ?? '#1a1a2e');
                @endphp
                <div
                    class="relative shadow-2xl rounded-lg overflow-hidden"
                    style="width: 800px; height: 450px; background-color: {{ $bgColor }}; flex-shrink: 0;"
                >
                    <!-- Locked Header -->
                    @if(isset($lockedZones['header']))
                    <div
                        class="absolute top-0 left-0 right-0 flex items-center px-6"
                        style="height: {{ ($lockedZones['header']['height_px'] ?? 80) * 0.42 }}px; background-color: {{ $lockedZones['header']['background'] ?? '#111' }}; border-bottom: 1px solid rgba(255,255,255,0.1);"
                    >
                        <div class="flex items-center gap-3">
                            @if($this->headerOptions['show_logo'] ?? true)
                                <div class="w-6 h-6 bg-indigo-500 rounded" title="Logo placeholder"></div>
                            @endif
                            @if($this->headerOptions['show_project_name'] ?? true)
                                <span style="color: {{ $lockedZones['header']['text_color'] ?? '#fff' }}; font-size: 11px; font-weight: 600;">
                                    {{ $this->presentation->project?->name ?? 'Project' }}
                                </span>
                            @endif
                        </div>
                        @if($this->headerOptions['show_date'] ?? false)
                            <span class="ml-auto" style="color: {{ $lockedZones['header']['text_color'] ?? '#fff' }}; font-size: 10px; opacity: 0.7;">
                                {{ now()->format('Y-m-d') }}
                            </span>
                        @endif
                        <span class="ml-auto text-xs opacity-40" style="color: {{ $lockedZones['header']['text_color'] ?? '#fff' }};">🔒 Header (locked)</span>
                    </div>
                    @endif

                    <!-- Content Area -->
                    <div
                        class="absolute left-0 right-0 px-8 py-4 overflow-hidden"
                        style="
                            top: {{ ($lockedZones['header']['height_px'] ?? 80) * 0.42 }}px;
                            bottom: {{ ($lockedZones['footer']['height_px'] ?? 60) * 0.42 }}px;
                        "
                    >
                        @forelse($activeSlide->slots as $slot)
                        <div class="mb-3 group relative">
                            @if($slot->slot_type === 'text')
                                <div style="color: {{ $lockedZones['header']['text_color'] ?? '#fff' }};" class="text-xs">
                                    <span class="text-xs opacity-40">{{ $slot->slot_key }}:</span>
                                    <p class="{{ $slot->slot_key === 'title' ? 'text-lg font-bold' : 'text-sm' }}">
                                        {{ $slot->content ?: '(empty)' }}
                                    </p>
                                </div>
                            @elseif($slot->slot_type === 'chart')
                                <div class="bg-white/10 rounded p-3 text-center">
                                    <p class="text-xs opacity-50">📊 Chart: {{ $slot->slot_key }}</p>
                                    @if($slot->content)
                                    @php $chartData = is_array($slot->content_decoded) ? $slot->content_decoded : []; @endphp
                                    @if(isset($chartData['labels']))
                                    <div class="flex gap-2 mt-2 flex-wrap justify-center">
                                        @foreach($chartData['labels'] as $i => $label)
                                        <span class="text-xs bg-indigo-500/30 px-2 py-1 rounded">
                                            {{ $label }}: {{ $chartData['values'][$i] ?? '' }}
                                        </span>
                                        @endforeach
                                    </div>
                                    @endif
                                    @endif
                                </div>
                            @endif

                            @if(!$slot->is_locked && !$activeSlide->is_locked)
                            <button
                                wire:click="regenerateSlot({{ $activeSlide->id }}, '{{ $slot->slot_key }}')"
                                class="absolute top-0 right-0 hidden group-hover:block text-xs bg-indigo-500 text-white px-2 py-1 rounded opacity-80"
                            >↻</button>
                            @endif
                        </div>
                        @empty
                        <p class="text-center opacity-50 mt-16 text-sm" style="color: white;">
                            No slots yet. Click "Generate" to fill this slide.
                        </p>
                        @endforelse
                    </div>

                    <!-- Locked Footer -->
                    @if(isset($lockedZones['footer']))
                    <div
                        class="absolute bottom-0 left-0 right-0 flex items-center px-6"
                        style="height: {{ ($lockedZones['footer']['height_px'] ?? 60) * 0.42 }}px; background-color: {{ $lockedZones['footer']['background'] ?? '#111' }}; border-top: 1px solid rgba(255,255,255,0.1);"
                    >
                        <span class="text-xs opacity-40" style="color: {{ $lockedZones['footer']['text_color'] ?? '#888' }};">🔒 Footer (locked)</span>
                        <div class="ml-auto flex items-center gap-3">
                            @if($this->footerOptions['show_confidentiality'] ?? false)
                                <span style="color: {{ $lockedZones['footer']['text_color'] ?? '#888' }}; font-size: 9px;">CONFIDENTIAL</span>
                            @endif
                            @if($this->footerOptions['show_page_number'] ?? true)
                                <span style="color: {{ $lockedZones['footer']['text_color'] ?? '#888' }}; font-size: 10px;">
                                    {{ $activeSlide->order }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Slot editor below preview -->
            <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 max-h-48 overflow-y-auto">
                <h3 class="text-sm font-medium mb-3">Edit Slots Manually</h3>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($activeSlide->slots as $slot)
                    @if(!$slot->is_locked)
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">{{ $slot->slot_key }} ({{ $slot->slot_type }})</label>
                        <textarea
                            class="w-full text-xs border border-gray-200 dark:border-gray-600 rounded p-2 bg-gray-50 dark:bg-gray-700 resize-none"
                            rows="2"
                            wire:change="updateSlotContent({{ $slot->id }}, $event.target.value)"
                        >{{ $slot->content }}</textarea>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @else
            <div class="flex-1 flex items-center justify-center text-gray-400">
                <div class="text-center">
                    <p class="text-2xl mb-4">👈</p>
                    <p>Select or add a slide to start</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
