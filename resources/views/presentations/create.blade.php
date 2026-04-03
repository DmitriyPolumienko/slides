@extends('layouts.app')

@section('title', 'New Presentation')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Create New Presentation</h1>

    <form action="{{ route('presentations.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow p-8 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-2">Title *</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-700"
                placeholder="Q1 Sales Report">
            @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Language</label>
                <select name="language_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-700">
                    <option value="">Select language</option>
                    @foreach($languages as $lang)
                    <option value="{{ $lang->id }}" {{ old('language_id') == $lang->id ? 'selected' : '' }}>
                        {{ $lang->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Project</label>
                <select name="project_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-700">
                    <option value="">Select project</option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Theme</label>
                <select name="theme_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-700">
                    <option value="">Select theme</option>
                    @foreach($themes as $theme)
                    <option value="{{ $theme->id }}" {{ old('theme_id') == $theme->id ? 'selected' : '' }}>
                        {{ $theme->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Master Template</label>
                <select name="master_template_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-700">
                    <option value="">Select template</option>
                    @foreach($templates as $template)
                    <option value="{{ $template->id }}" {{ old('master_template_id') == $template->id ? 'selected' : '' }}>
                        {{ $template->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700">
            Create Presentation
        </button>
    </form>
</div>
@endsection
