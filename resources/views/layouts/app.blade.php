<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: (() => { try { return localStorage.getItem('darkMode') === 'true'; } catch (error) { return false; } })() }" x-init="$watch('darkMode', val => { try { localStorage.setItem('darkMode', val); } catch (error) {} })" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
        @include('partials.pwa-head')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta property="csp-nonce" nonce="{{ Vite::cspNonce() }}">

        <title>{{ config('app.name', 'CivicGuard') }}</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="preload" href="{{ Vite::asset('resources/fonts/Figtree-400.woff2') }}" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="{{ Vite::asset('resources/fonts/Bitter-600.woff2') }}" as="font" type="font/woff2" crossorigin>

        <style>
            [x-cloak] { display: none !important; }
        </style>
        <script nonce="{{ Vite::cspNonce() }}">
            try {
                if (localStorage.getItem('darkMode') === 'true') {
                    document.documentElement.classList.add('dark');
                }
            } catch (error) {}
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body data-cg-route="app" :class="{ 'dark': darkMode }" class="font-sans antialiased text-ink dark:text-gray-100">
        <a href="#main-content" class="sr-only fixed left-4 top-4 z-[100] rounded-lg bg-maroon-700 px-4 py-3 font-semibold text-white focus:not-sr-only">
            {{ __('Skip to content') }}
        </a>
        <div x-data="appShell" :class="{ 'cg-sidebar-collapsed': collapsed }" class="cg-app-shell min-h-screen flex flex-col lg:flex-row">
            <div class="cg-app-sidebar pointer-events-none sticky top-0 z-30 lg:pointer-events-auto lg:static lg:z-auto lg:shrink-0" :class="collapsed ? 'lg:w-[76px]' : 'lg:w-64'">
                @include('layouts.navigation')
            </div>

            <div class="flex-1 min-w-0 flex flex-col">
                @isset($header)
                    <header class="cg-app-header border-b">
                        <div class="{{ isset($headerWidth) ? trim((string) $headerWidth) : 'max-w-6xl' }} mx-auto px-4 py-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between gap-4">
                                <div class="min-w-0 flex-1 pl-0 [&>h2]:text-lg [&>h2]:font-semibold [&>h2]:tracking-tight sm:[&>h2]:text-xl">
                                    {{ $header }}
                                </div>
                                <div class="hidden lg:flex items-center gap-3 shrink-0">
                                    <x-notification-dropdown />
                                </div>
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
