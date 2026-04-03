@extends('layouts.app')

@section('title', 'Presentations')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <h1 class="text-3xl font-bold">My Presentations</h1>
    <a href="{{ route('presentations.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
        + New Presentation
    </a>
</div>

@if($presentations->isEmpty())
    <div class="text-center py-16 text-gray-500">
        <p class="text-xl mb-4">No presentations yet.</p>
        <a href="{{ route('presentations.create') }}" class="text-indigo-600 hover:underline">Create your first one →</a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($presentations as $presentation)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="font-semibold text-lg mb-2">{{ $presentation->title }}</h3>
            <div class="text-sm text-gray-500 space-y-1 mb-4">
                <p>Project: {{ $presentation->project?->name ?? '—' }}</p>
                <p>Theme: {{ $presentation->theme?->name ?? '—' }}</p>
                <p>Status: <span class="capitalize">{{ $presentation->status }}</span></p>
                <p>Slides: {{ $presentation->slides_count ?? 0 }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('presentations.builder', $presentation) }}" class="flex-1 text-center bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 text-sm">
                    Open Builder
                </a>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg text-sm">
                        ⋮
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-1 bg-white dark:bg-gray-700 border rounded-lg shadow-lg z-10 min-w-[140px]">
                        <a href="{{ route('presentations.export.pdf', $presentation) }}" class="block px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-600">PDF</a>
                        <a href="{{ route('presentations.export.pptx', $presentation) }}" class="block px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-600">PPTX</a>
                        <a href="{{ route('presentations.export.figma', $presentation) }}" class="block px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-600">Figma JSON</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $presentations->links() }}</div>
@endif
@endsection
