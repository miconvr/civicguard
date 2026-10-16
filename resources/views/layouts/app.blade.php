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
                        <div class="{{ isset($headerWidth) ? trim((string) $headerWidth) : 'max-w-6xl' }} mx-auto px-4 py-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between gap-4">
                                <div class="min-w-0 flex-1 pl-0 [&>h2]:text-lg [&>h2]:font-semibold [&>h2]:tracking-tight sm:[&>h2]:text-xl">
                                    {{ $header }}
                                </div>
                                <div class="hidden lg:flex items-center gap-3 shrink-0">
                                    @php
                                        $headerUnreadCount = $unreadCount ?? (auth()->check()
                                            ? \App\Models\AppNotification::where('user_id', auth()->id())->where('is_read', false)->count()
                                            : 0);
                                    @endphp
                                    <a href="{{ route('notifications.index') }}"
                                       class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-stone-200 bg-white text-stone-700 shadow-sm transition hover:border-maroon-700 hover:text-maroon-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-200 dark:hover:bg-white/10 dark:hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-maroon-700 {{ request()->routeIs('notifications.index') ? '!border-maroon-700 !text-maroon-700 dark:!border-maroon-500 dark:!text-white' : '' }}"
                                       title="{{ __('Notifications') }}"
                                       aria-label="{{ __('Notifications') }}">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                        </svg>
                                        @if ($headerUnreadCount > 0)
                                            <span class="absolute -top-1 -right-1 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-maroon-700 px-1 text-[11px] font-bold text-white shadow-sm ring-2 ring-white dark:bg-maroon-600 dark:ring-gray-900">
                                                {{ $headerUnreadCount > 99 ? '99+' : $headerUnreadCount }}
                                                <span class="sr-only"> {{ __('unread') }}</span>
                                            </span>
                                        @endif
                                    </a>
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