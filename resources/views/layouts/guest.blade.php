<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CivicGuard') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-gray-50 to-gray-100 dark:from-[#1f1f1f] dark:to-[#292929]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

            <!-- Sleek Logo and Title -->
            <div class="text-center mb-4">
                <a href="/">
                    <x-application-logo class="w-16 h-16 fill-current text-maroon-700 dark:text-gray-100 mx-auto" />
                </a>
                <h1 class="mt-2 text-3xl font-bold text-maroon-700 dark:text-gray-100 tracking-tight">CivicGuard</h1>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 font-semibold uppercase tracking-widest">Barangay Maimpis Incident Management</p>
            </div>

            <!-- Auth Card -->
            <div class="w-full sm:max-w-md mt-2 px-6 py-8 bg-white dark:bg-[#252525] shadow-lg overflow-hidden sm:rounded-xl border border-gray-100 dark:border-gray-700">
                {{ $slot }}
            </div>

            <!-- Developer Credits -->
            <div class="mt-8 w-full max-w-md px-4">
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 text-center tracking-widest uppercase mb-2 font-semibold">Proudly Developed By</p>
                    <div class="flex flex-wrap justify-center gap-x-2 gap-y-1 text-xs text-gray-600 dark:text-gray-300 font-medium">
                        <span>Mico Gerard Navarro</span>
                        <span class="text-gray-300 dark:text-gray-600">&bull;</span>
                        <span>Franz Mikey Reyes</span>
                        <span class="text-gray-300 dark:text-gray-600">&bull;</span>
                        <span>Carl Spencer</span>
                        <span class="text-gray-300 dark:text-gray-600">&bull;</span>
                        <span>Adriann Enriquez</span>
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>