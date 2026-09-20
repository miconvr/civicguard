<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CivicGuard') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-maroon-800 via-maroon-700 to-maroon-900 dark:from-gray-900 dark:via-gray-900 dark:to-maroon-900">
            <div class="flex flex-col items-center">
                <a href="/" class="bg-white rounded-full p-3 shadow-lg">
                    <x-application-logo class="w-16 h-16 text-maroon-700" />
                </a>
                <h1 class="mt-4 text-2xl font-bold text-white">CivicGuard</h1>
                <p class="text-xs uppercase tracking-widest text-gold-300">Barangay Maimpis Incident Management</p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-6 bg-white dark:bg-gray-800 shadow-xl overflow-hidden sm:rounded-xl border-t-4 border-gold-500">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>