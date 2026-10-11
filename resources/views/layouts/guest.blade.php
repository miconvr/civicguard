<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: (() => { try { return localStorage.getItem('darkMode') === 'true'; } catch (error) { return false; } })() }" x-init="$watch('darkMode', val => { try { localStorage.setItem('darkMode', val); } catch (error) {} })" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CivicGuard') }}</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        <style>
            [x-cloak] { display: none !important; }
        </style>
        <script>
            try {
                if (localStorage.getItem('darkMode') === 'true') document.documentElement.classList.add('dark');
            } catch (error) {}
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800|bitter:600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body :class="{ 'dark': darkMode }" class="font-sans text-ink antialiased bg-paper dark:bg-[#1c1c1b]">
        <button type="button" @click="darkMode = !darkMode" :aria-pressed="darkMode.toString()"
                :aria-label="darkMode ? '{{ __('Switch to light mode') }}' : '{{ __('Switch to dark mode') }}'"
                class="fixed right-4 top-4 z-10 inline-flex min-h-11 items-center gap-2 rounded-full border border-stone-300 bg-white/90 px-4 text-sm font-semibold text-stone-700 shadow-sm backdrop-blur transition hover:border-maroon-600 hover:text-maroon-800 dark:border-[#454541] dark:bg-[#30302e]/90 dark:text-gray-100 dark:hover:border-gray-400">
            <svg x-show="!darkMode" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="12" cy="12" r="4"></circle><path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"></path>
            </svg>
            <svg x-show="darkMode" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.9 13A8.5 8.5 0 0 1 11 3.1 8.5 8.5 0 1 0 20.9 13Z"></path>
            </svg>
            <span x-text="darkMode ? '{{ __('Dark') }}' : '{{ __('Light') }}'"></span>
        </button>
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-20 sm:py-12">

            <div class="text-center mb-4">
                <a href="/">
                    <x-application-logo class="w-16 h-16 fill-current text-maroon-700 dark:text-gray-100 mx-auto" />
                </a>
                <h1 class="mt-2 text-3xl font-bold text-maroon-700 dark:text-gray-100 tracking-tight">CivicGuard</h1>
                <p class="text-sm text-stone-600 dark:text-gray-300 mt-1">Barangay Maimpis Incident Management</p>
            </div>

            <div class="w-full sm:max-w-md mt-2 px-6 py-8 bg-white dark:bg-[#262624] shadow-xl shadow-stone-900/5 dark:shadow-black/20 overflow-hidden rounded-2xl border border-stone-200 dark:border-[#3d3d3a]">
                {{ $slot }}
            </div>

            <div class="mt-8 w-full max-w-md px-4">
                <div class="border-t border-stone-300 dark:border-gray-700 pt-4">
                    <p class="text-xs text-stone-400 dark:text-gray-500 text-center mb-2">Developed by</p>
                    <div class="flex flex-wrap justify-center gap-x-2 gap-y-1 text-xs text-stone-600 dark:text-gray-300 font-medium">
                        <span>Mico Gerard Navarro</span>
                        <span class="text-stone-300 dark:text-gray-600">&bull;</span>
                        <span>Franz Mikey Reyes</span>
                        <span class="text-stone-300 dark:text-gray-600">&bull;</span>
                        <span>Carl Spencer Talon</span>
                        <span class="text-stone-300 dark:text-gray-600">&bull;</span>
                        <span>Adriann Enriquez</span>
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>