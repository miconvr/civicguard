<!DOCTYPE html>
<html lang="en" x-data="landingPage" :lang="language === 'tl' ? 'tl' : 'en'" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f7f3ed">
        <meta name="description" content="CivicGuard helps Barangay Maimpis residents report incidents and helps local teams respond with care.">
        <title>{{ config('app.name', 'CivicGuard') }} · Barangay Maimpis</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <script>
            try {
                if (localStorage.getItem('darkMode') === 'true') document.documentElement.classList.add('dark');
            } catch (error) {}
        </script>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800|bitter:600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body :class="{ 'dark': darkMode }" class="min-h-screen bg-paper font-sans text-ink antialiased dark:bg-[#1c1c1b] dark:text-gray-100">
        <a href="#main-content" class="sr-only fixed left-4 top-4 z-50 rounded-lg bg-maroon-700 px-4 py-3 font-semibold text-white focus:not-sr-only">{{ __('Skip to content') }}</a>
        <header class="relative z-10 border-b border-stone-200/80 bg-white/80 backdrop-blur-xl dark:border-[#3d3d3a] dark:bg-[#222221]/95">
            <nav class="mx-auto flex min-h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8" aria-label="{{ __('Main navigation') }}">
                <a href="{{ url('/') }}" class="flex shrink-0 items-center gap-2 rounded-lg focus-visible:outline-offset-4 sm:gap-3">
                    <x-application-logo class="h-9 w-9 shrink-0 text-maroon-700 dark:text-[#c27872] sm:h-10 sm:w-10" />
                    <span class="min-w-0 leading-tight">
                        <span class="block whitespace-nowrap text-sm font-bold text-maroon-900 dark:text-white sm:text-base">CivicGuard</span>
                        <span class="hidden truncate text-xs text-stone-600 dark:text-gray-300 sm:block">{{ __('Barangay Maimpis') }}</span>
                    </span>
                </a>
                <div class="flex shrink-0 items-center gap-1 sm:gap-3">
                    <button type="button" @click="darkMode = !darkMode" :aria-pressed="darkMode.toString()"
                            :aria-label="darkMode ? '{{ __('Switch to light mode') }}' : '{{ __('Switch to dark mode') }}'"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-stone-300 bg-white text-stone-700 transition hover:border-maroon-600 hover:text-maroon-800 dark:border-[#454541] dark:bg-[#30302e] dark:text-gray-100 dark:hover:border-gray-400 sm:h-11 sm:w-11"
                            title="{{ __('Toggle color theme') }}">
                        <svg x-show="!darkMode" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="4"></circle><path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"></path>
                        </svg>
                        <svg x-show="darkMode" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.9 13A8.5 8.5 0 0 1 11 3.1 8.5 8.5 0 1 0 20.9 13Z"></path>
                        </svg>
                    </button>
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-full bg-maroon-700 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-maroon-800 sm:px-5"><span x-text="text('dashboard')">{{ __('Go to your dashboard') }}</span></a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-full px-2 text-sm font-semibold text-stone-700 transition hover:bg-stone-100 hover:text-maroon-800 dark:text-gray-200 dark:hover:bg-white/10 dark:hover:text-white sm:px-4"><span x-text="text('signIn')">{{ __('Sign in') }}</span></a>
                        <a href="{{ route('register') }}" :aria-label="text('createAccount')" class="inline-flex min-h-11 items-center justify-center rounded-full bg-maroon-700 px-3 text-sm font-semibold text-white shadow-sm transition hover:bg-maroon-800 sm:px-5">
                            <span class="sm:hidden" x-text="text('join')">{{ __('Join') }}</span>
                            <span class="hidden sm:inline" x-text="text('createAccount')">{{ __('Create a resident account') }}</span>
                        </a>
                    @endauth
                </div>
            </nav>
        </header>

        <main id="main-content">
            <div class="mx-auto flex max-w-7xl justify-end px-4 pt-4 sm:px-6 lg:px-8">
                <div class="inline-flex rounded-lg border border-stone-300 bg-white p-1 dark:border-[#454541] dark:bg-[#262624]" role="group" :aria-label="text('chooseLanguage')">
                    <button type="button" @click="setLanguage('en')" :aria-pressed="language === 'en'" class="min-h-9 rounded-md px-3 text-sm font-medium text-stone-700 transition aria-pressed:bg-maroon-700 aria-pressed:text-white dark:text-gray-200">
                        English
                    </button>
                    <button type="button" @click="setLanguage('tl')" :aria-pressed="language === 'tl'" class="min-h-9 rounded-md px-3 text-sm font-medium text-stone-700 transition aria-pressed:bg-maroon-700 aria-pressed:text-white dark:text-gray-200">
                        Tagalog
                    </button>
                </div>
            </div>
            <section>
                <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-[1.1fr_0.9fr] lg:gap-16 lg:px-8 lg:py-28">
                    <div class="max-w-2xl">
                        <p class="mb-4 text-sm font-semibold text-maroon-800 dark:text-[#c27872]">
                            <span x-text="text('community')">{{ __('For the people of Barangay Maimpis') }}</span>
                        </p>
                        <h1 class="max-w-xl text-4xl font-bold leading-tight text-stone-950 sm:text-5xl lg:text-6xl dark:text-white">
                            <span x-text="text('headline')">{{ __('Let’s take care of our barangay.') }}</span>
                            <span class="text-maroon-700 dark:text-[#c27872]" x-text="text('headlineAccent')">{{ __('Together.') }}</span>
                        </h1>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-stone-700 dark:text-gray-300">
                            <span x-text="text('introduction')">{{ __('Share a concern, follow its updates, and connect with our barangay officials and tanod to help keep Maimpis safe.') }}</span>
                        </p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-maroon-700 px-6 font-semibold text-white shadow-sm transition hover:bg-maroon-800">
                                    <span x-text="text('dashboard')">{{ __('Go to your dashboard') }}</span>
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-maroon-700 px-6 font-semibold text-white shadow-sm transition hover:bg-maroon-800">
                                    <span x-text="text('createAccount')">{{ __('Create a resident account') }}</span>
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-stone-300 bg-white/80 px-6 font-semibold text-stone-800 transition hover:border-maroon-400 hover:bg-white dark:border-[#454541] dark:bg-[#262624] dark:text-gray-100 dark:hover:bg-[#30302e]">
                                    <span x-text="text('signIn')">{{ __('Sign in') }}</span>
                                </a>
                            @endauth
                        </div>
                        <p class="mt-4 text-sm text-stone-600 dark:text-gray-400">
                            <span x-text="text('emergency')">{{ __('Emergency? Call 911. CivicGuard reports are not monitored in real time.') }}</span>
                        </p>
                    </div>

                    <div class="mx-auto w-full max-w-lg">
                        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8 dark:border-[#3d3d3a] dark:bg-[#262624]">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-stone-600 dark:text-gray-300" x-text="text('processIntro')">{{ __('From our kababayans, for our barangay') }}</p>
                                    <h2 class="mt-1 text-2xl font-bold text-stone-950 dark:text-white" x-text="text('processTitle')">{{ __('Every concern has a next step.') }}</h2>
                                </div>
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-maroon-50 text-maroon-700 dark:bg-[#3a2828] dark:text-[#c27872]" aria-hidden="true">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12.5 9 17l11-11"></path>
                                        <path stroke-linecap="round" d="M12 3v2m0 14v2M3 12h2m14 0h2"></path>
                                    </svg>
                                </span>
                            </div>
                            <ol class="mt-8 space-y-5">
                                @foreach ([
                                    ['01', 'reportStep', 'reportDescription'],
                                    ['02', 'staffStep', 'staffDescription'],
                                    ['03', 'progressStep', 'progressDescription'],
                                ] as [$step, $title, $description])
                                    <li class="flex gap-4">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-maroon-50 text-sm font-bold text-maroon-800 dark:bg-[#3a2828] dark:text-[#c27872]">{{ $step }}</span>
                                        <span>
                                            <span class="block font-semibold text-stone-900 dark:text-gray-100" x-text="text('{{ $title }}')">{{ ['reportStep' => __('Share your concern'), 'staffStep' => __('Barangay staff follow up'), 'progressStep' => __('Follow the updates')][$title] }}</span>
                                            <span class="mt-1 block text-sm leading-6 text-stone-600 dark:text-gray-400" x-text="text('{{ $description }}')">{{ ['reportDescription' => __('Add the details and location so the right team can review it.'), 'staffDescription' => __('Our officials and tanod review and respond to reports.'), 'progressDescription' => __('Check the status and let us know when the concern is resolved.')][$description] }}</span>
                                        </span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="border-y border-stone-200 bg-white/65 py-16 dark:border-[#3d3d3a] dark:bg-[#222221]">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <h2 class="text-3xl font-bold text-stone-950 dark:text-white" x-text="text('howItWorks')">{{ __('How we can help one another') }}</h2>
                        <p class="mt-3 leading-7 text-stone-600 dark:text-gray-300" x-text="text('teamwork')">{{ __('Working together helps the barangay respond more effectively.') }}</p>
                    </div>
                    <div class="mt-8 grid gap-5 md:grid-cols-2">
                        <article class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm dark:border-[#3d3d3a] dark:bg-[#262624]">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-maroon-50 text-maroon-700 dark:bg-[#3a2828] dark:text-[#c27872]" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25s7.5-3.75 7.5-9V5.5L12 2.75 4.5 5.5v5.75c0 5.25 7.5 9 7.5 9Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9 11.5 2 2 4-4"/></svg>
                            </span>
                            <h3 class="mt-4 text-lg font-bold text-stone-950 dark:text-white" x-text="text('residentsTitle')">{{ __('For residents') }}</h3>
                            <p class="mt-2 text-sm leading-6 text-stone-600 dark:text-gray-300" x-text="text('residentDescription')">{{ __('Report an incident, check its progress, and receive important updates from the barangay.') }}</p>
                            @guest
                                <a href="{{ route('register') }}" class="mt-5 inline-flex min-h-11 items-center font-semibold text-maroon-800 hover:underline dark:text-[#c27872]"><span x-text="text('getStarted')">{{ __('Get started') }}</span> <span class="ml-2" aria-hidden="true">&rarr;</span></a>
                            @else
                                <a href="{{ route('dashboard') }}" class="mt-5 inline-flex min-h-11 items-center font-semibold text-maroon-800 hover:underline dark:text-[#c27872]"><span x-text="text('dashboard')">{{ __('Go to your dashboard') }}</span> <span class="ml-2" aria-hidden="true">&rarr;</span></a>
                            @endguest
                        </article>
                        <article class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm dark:border-[#3d3d3a] dark:bg-[#262624]">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-800 dark:bg-amber-400/10 dark:text-amber-200" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3"/><circle cx="12" cy="12" r="9"/></svg>
                            </span>
                            <h3 class="mt-4 text-lg font-bold text-stone-950 dark:text-white" x-text="text('staffTitle')">{{ __('For barangay officials and tanod') }}</h3>
                            <p class="mt-2 text-sm leading-6 text-stone-600 dark:text-gray-300" x-text="text('staffDescription')">{{ __('Review reports, prioritize urgent concerns, and keep residents updated.') }}</p>
                            <a href="{{ route('login') }}" class="mt-5 inline-flex min-h-11 items-center font-semibold text-maroon-800 hover:underline dark:text-[#c27872]"><span x-text="text('staffSignIn')">{{ __('Staff sign in') }}</span> <span class="ml-2" aria-hidden="true">&rarr;</span></a>
                        </article>
                    </div>
                </div>
            </section>
        </main>

        <footer class="py-8">
            <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 text-sm text-stone-600 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8 dark:text-gray-400">
                <p><span class="font-semibold text-stone-800 dark:text-gray-200">CivicGuard</span> · <span x-text="text('footerBrand')">{{ __('Barangay Maimpis Incident Management') }}</span></p>
                <p x-text="text('footerEmergency')">{{ __('For emergencies, call 911.') }}</p>
            </div>
        </footer>
    </body>
</html>
