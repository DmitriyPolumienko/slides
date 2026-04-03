<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Slides') }}@hasSection('title') - @yield('title')@endif</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen">
    <!-- Theme Toggle -->
    <div class="fixed top-4 right-4 z-50">
        <button
            @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full p-2 shadow-lg"
            title="Toggle theme"
        >
            <span x-show="!darkMode">🌙</span>
            <span x-show="darkMode">☀️</span>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="{{ route('presentations.index') }}" class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                🎨 Slides Builder
            </a>
            <div class="flex gap-4">
                <a href="{{ route('presentations.index') }}" class="text-gray-600 dark:text-gray-300 hover:text-indigo-600">Presentations</a>
                <a href="{{ route('presentations.create') }}" class="bg-indigo-600 text-white px-4 py-1 rounded-lg hover:bg-indigo-700">+ New</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-8">
        @yield('content')
    </main>

    @livewireScripts
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', ({ message, type }) => {
                const div = document.createElement('div');
                div.className = `fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white z-50 ${type === 'error' ? 'bg-red-500' : 'bg-green-500'}`;
                div.textContent = message;
                document.body.appendChild(div);
                setTimeout(() => div.remove(), 3000);
            });
        });
    </script>
</body>
</html>
