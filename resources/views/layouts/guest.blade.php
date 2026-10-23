<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body data-cg-route="auth" class="cg-auth-shell font-sans text-ink antialiased">
        <main class="cg-auth-layout mx-auto flex min-h-[100svh] w-full max-w-lg flex-col items-center justify-center gap-6 px-4 py-10 sm:px-6">
            <section class="w-full" aria-label="{{ __('Account access') }}">
                <div class="cg-auth-card cg-page-enter cg-page-enter-late mx-auto w-full overflow-hidden border px-6 pb-8 pt-9 sm:px-8">
                    <a href="/" class="mb-7 flex items-center justify-center gap-3 border-b border-stone-200 pb-7" aria-label="CivicGuard, Brgy. Maimpis home">
                        <x-application-logo class="h-11 w-auto shrink-0" />
                        <span class="leading-tight">
                            <span class="block font-sans text-xl font-bold text-maroon-800">CivicGuard</span>
                            <span class="block text-[11px] uppercase tracking-wider text-stone-600 dark:text-stone-300">Brgy. Maimpis</span>
                        </span>
                    </a>
                    {{ $slot }}
                </div>
            </section>

            <p class="cg-auth-emergency text-center text-sm">{{ __('In immediate danger? Call 911 first.') }}</p>
        </main>
    </body>
</html>
