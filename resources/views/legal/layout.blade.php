<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title') - {{ config('app.name', 'CivicGuard') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-white text-gray-900 antialiased">
    <main class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
        <a href="{{ url('/') }}" class="text-sm font-semibold text-maroon-700 hover:underline">&larr; CivicGuard</a>
        <h1 class="mt-6 text-3xl font-bold">@yield('title')</h1>
        <p class="mt-1 text-sm text-gray-600">Last updated: @yield('updated')</p>
        <article class="mt-6 space-y-3 text-[15px] leading-7">
            @yield('content')
        </article>
        <p class="mt-10 border-t pt-4 text-sm text-gray-600">
            <a class="underline" href="{{ route('privacy') }}">Privacy Policy</a> &middot;
            <a class="underline" href="{{ route('terms') }}">Terms of Use</a>
        </p>
    </main>
</body>
</html>
