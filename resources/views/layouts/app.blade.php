<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CivicGuard') }}</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        <style>
            html, body { background-color: #f9fafb; }
            html.dark, html.dark body { background-color: #181818; }
        </style>
        <script>
            try {
                if (localStorage.getItem('darkMode') === 'true') {
                    document.documentElement.classList.add('dark');
                }
            } catch (error) {}
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
        <noscript><link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" /></noscript>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50 dark:bg-[#181818]">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white dark:bg-[#202020] border-b border-gray-200 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        <div class="border-l-4 border-maroon-700 dark:border-white pl-4">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>

            <footer class="py-6 text-center text-xs text-gray-400 dark:text-gray-500">
                CivicGuard &middot; Barangay Maimpis Incident Management System
            </footer>
        </div>
    </body>
</html>
