<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CivicGuard') }}</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        <style>
            [x-cloak] { display: none !important; }
        </style>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800|bitter:600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="cg-auth-shell font-sans text-ink antialiased">
        <main class="cg-auth-layout mx-auto flex min-h-[100svh] w-full max-w-lg flex-col items-center justify-center gap-6 px-4 py-10 sm:px-6">
            <div class="cg-auth-brand cg-page-enter w-full text-center">
                <a href="/" class="cg-auth-mark">
                    <x-application-logo class="h-10 w-10" />
                </a>
                <h1 class="mt-4 font-sans text-3xl font-bold tracking-tight text-white">CivicGuard</h1>
                <p class="mt-1 text-sm">Barangay Maimpis Incident Management</p>
                <div class="cg-auth-emergency mx-auto mt-5 max-w-sm border-t pt-4 text-sm leading-6">
                    <p>{{ __('In immediate danger? Call 911 first.') }}</p>
                </div>
            </div>

            <section class="w-full" aria-label="{{ __('Account access') }}">
                <div class="cg-auth-card cg-page-enter cg-page-enter-late mx-auto w-full overflow-hidden border px-6 py-8 sm:px-8">
                    {{ $slot }}
                </div>
            </section>

            <div class="cg-auth-credits mt-6 w-full px-2">
                <div class="border-t pt-4">
                    <p class="mb-2 text-center text-xs">Developed by</p>
                    <div class="flex flex-wrap justify-center gap-x-2 gap-y-1 text-xs font-medium">
                        <span>Mico Gerard Navarro</span>
                        <span aria-hidden="true">&bull;</span>
                        <span>Franz Mikey Reyes</span>
                        <span aria-hidden="true">&bull;</span>
                        <span>Carl Spencer Talon</span>
                        <span aria-hidden="true">&bull;</span>
                        <span>Adriann Enriquez</span>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>