<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: (() => { try { return localStorage.getItem('darkMode') === 'true'; } catch (error) { return false; } })() }" x-init="$watch('darkMode', val => { try { localStorage.setItem('darkMode', val); } catch (error) {} })" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
        @include('partials.pwa-head')
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CivicGuard') }}</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        <style>
            [x-cloak] { display: none !important; }
        </style>
        <script>
            try {
                if (localStorage.getItem('darkMode') === 'true') {
                    document.documentElement.classList.add('dark');
                }
            } catch (error) {}
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|bitter:600,700&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
        <noscript><link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|bitter:600,700&display=swap" rel="stylesheet" /></noscript>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body :class="{ 'dark': darkMode }" class="font-sans antialiased text-ink dark:text-gray-100">
        <a href="#main-content" class="sr-only fixed left-4 top-4 z-[100] rounded-lg bg-maroon-700 px-4 py-3 font-semibold text-white focus:not-sr-only">
            {{ __('Skip to content') }}
        </a>
        <div x-data="appShell" :class="{ 'cg-sidebar-collapsed': collapsed }" class="cg-app-shell min-h-screen flex flex-col lg:flex-row">
            <div class="cg-app-sidebar sticky top-0 z-30 lg:static lg:z-auto lg:shrink-0" :class="collapsed ? 'lg:w-[76px]' : 'lg:w-64'">
                @include('layouts.navigation')
            </div>

            <div class="flex-1 min-w-0 flex flex-col">
                @isset($header)
                    <header class="cg-app-header border-b">
                        <div class="{{ isset($headerWidth) ? trim((string) $headerWidth) : 'max-w-6xl' }} mx-auto px-4 py-5 sm:px-6 lg:px-8">
                            <div class="pl-0 [&>h2]:text-lg [&>h2]:font-semibold [&>h2]:tracking-tight sm:[&>h2]:text-xl">
                                {{ $header }}
                            </div>
                        </div>
                    </header>
                @endisset

                <main id="main-content" class="{{ isset($motion) && trim((string) $motion) === 'off' ? '' : 'cg-page-enter' }} flex-1">
                    {{ $slot }}
                </main>

                <footer class="py-6 text-center text-xs text-stone-600 dark:text-gray-400">
                    CivicGuard &middot; Barangay Maimpis Incident Management System
                </footer>
            </div>
        </div>
    </body>
</html>