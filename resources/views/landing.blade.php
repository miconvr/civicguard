<!DOCTYPE html>
<html lang="en" x-data="landingPage" :lang="language">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f7f3ed">
        <meta name="description" content="CivicGuard helps Barangay Maimpis residents report incidents and follow updates from barangay staff.">
        <title>{{ config('app.name', 'CivicGuard') }} · Barangay Maimpis</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <style>[x-cloak] { display: none !important; }</style>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|bitter:600,700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
        <noscript><link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|bitter:600,700&display=swap" rel="stylesheet"></noscript>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-[100dvh] bg-paper font-sans antialiased text-ink dark:bg-[#1c1c1b] dark:text-gray-100">
        <a href="#main-content" class="sr-only fixed left-4 top-4 z-[100] rounded-lg bg-maroon-700 px-4 py-3 font-semibold text-white focus:not-sr-only">
            {{ __('Skip to content') }}
        </a>

        <div class="min-h-[100dvh] overflow-hidden">
            <header class="border-b border-stone-200/80 bg-paper dark:border-[#3d3d3a] dark:bg-[#1c1c1b]">
                <nav aria-label="Main navigation" :aria-label="text('navigation')" class="mx-auto flex min-h-[72px] max-w-7xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                    <a href="{{ url('/') }}" class="group flex min-w-0 items-center gap-2.5 rounded-md focus-visible:outline-offset-4" aria-label="CivicGuard, Barangay Maimpis home" :aria-label="text('homeLink')">
                        <x-application-logo class="h-9 w-9 shrink-0" />
                        <span class="min-w-0">
                            <span class="block text-[15px] font-bold leading-tight tracking-tight text-maroon-900 dark:text-gray-100">CivicGuard</span>
                            <span class="block truncate text-[11px] leading-tight text-stone-600 dark:text-gray-400">Barangay Maimpis</span>
                        </span>
                    </a>

                    <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                        <button type="button"
                                @click="toggleLanguage()"
                                :aria-label="language === 'en' ? 'Switch language to Tagalog' : 'Switch language to English'"
                                :aria-pressed="(language === 'tl').toString()"
                                class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-md border border-stone-300 px-2.5 text-xs font-semibold tracking-wide text-stone-700 transition-colors hover:bg-white/70 dark:border-[#4a4945] dark:text-gray-200 dark:hover:bg-white/5">
                            <span x-text="language === 'en' ? 'EN' : 'TL'"></span>
                        </button>
                        @auth
                            <a href="{{ route('dashboard') }}" class="ml-1 inline-flex min-h-10 items-center justify-center rounded-md bg-maroon-700 px-3 text-xs font-semibold text-white transition-colors hover:bg-maroon-800 sm:px-4 sm:text-sm">
                                <span x-text="text('dashboard')">Go to your dashboard</span>
                            </a>
                        @endauth
                    </div>
                </nav>
            </header>

            <main id="main-content">
                <section class="mx-auto grid max-w-7xl gap-10 px-4 pb-14 pt-12 sm:px-6 sm:pb-16 sm:pt-16 lg:grid-cols-[1.1fr_0.9fr] lg:items-center lg:gap-16 lg:px-8 lg:pb-20 lg:pt-20">
                    <div class="max-w-3xl">
                        <h1 class="cg-intro-enter max-w-none text-[2.125rem] leading-[1.08] max-[360px]:text-[1.875rem] sm:max-w-[16ch] sm:text-6xl lg:max-w-[14ch]">
                            <span x-text="text('headline')">Report an issue in</span>
                            <span class="text-maroon-800 dark:text-[#d5aaa9]" x-text="text('headlineAccent')"> Barangay Maimpis.</span>
                        </h1>
                        <p class="cg-intro-enter cg-intro-enter-1 mt-5 max-w-xl text-base leading-7 text-stone-700 dark:text-gray-300 sm:text-lg sm:leading-8" x-text="text('introduction')">Report an incident, then follow its progress here. CivicGuard connects residents with barangay staff.</p>
                        <a href="#how-it-works" class="mt-5 inline-flex min-h-11 items-center rounded-md text-sm font-semibold text-stone-700 underline decoration-stone-400 underline-offset-4 transition-colors hover:text-maroon-800 dark:text-gray-200 dark:decoration-gray-500 dark:hover:text-white" x-text="text('howItWorks')">See how it works</a>
                    </div>

                    <aside id="get-started" aria-label="Choose how to continue" :aria-label="text('startLabel')" class="cg-intro-enter cg-intro-enter-2 rounded-xl border border-stone-300 bg-[#f2ede7] p-5 dark:border-[#454440] dark:bg-[#262624] sm:p-7">
                        <h2 class="text-xl font-semibold leading-tight sm:text-2xl" x-text="text('startTitle')">What would you like to do?</h2>
                        <div class="mt-5">
                            <h3 class="text-base font-semibold text-stone-950 dark:text-gray-100" x-text="text('residentPathTitle')">I live in Maimpis</h3>
                            <p class="mt-1.5 max-w-md text-sm leading-6 text-stone-700 dark:text-gray-300" x-text="text('residentPathDescription')">Create a resident account to report an issue and follow updates.</p>
                            @guest
                                <a href="{{ route('register') }}" class="cg-btn mt-4 w-full sm:w-auto">
                                    <span x-text="text('residentAction')">Create a resident account</span>
                                    <span aria-hidden="true">→</span>
                                </a>
                                <a href="{{ route('login') }}" class="mt-2 inline-flex min-h-10 items-center rounded-md text-sm font-semibold text-maroon-800 underline decoration-maroon-700/50 underline-offset-4 transition-colors hover:text-maroon-950 dark:text-[#e8c4c7] dark:decoration-[#c59da3] dark:hover:text-white" x-text="text('existingAccount')">Already have an account? Sign in</a>
                            @else
                                <a href="{{ route('dashboard') }}" class="cg-btn mt-4 w-full sm:w-auto">
                                    <span x-text="text('dashboard')">Go to your dashboard</span>
                                    <span aria-hidden="true">→</span>
                                </a>
                            @endguest
                        </div>
                        <div class="mt-5 border-t border-stone-300 pt-5 dark:border-[#454440]">
                            <h3 class="text-base font-semibold text-stone-950 dark:text-gray-100" x-text="text('staffPathTitle')">I work for the barangay</h3>
                            <p class="mt-1.5 max-w-md text-sm leading-6 text-stone-700 dark:text-gray-300" x-text="text('staffPathDescription')">Sign in to review reports and share updates with residents.</p>
                            <a href="{{ route('login') }}" class="mt-2 inline-flex min-h-10 items-center rounded-md text-sm font-semibold text-maroon-800 underline decoration-maroon-700/50 underline-offset-4 transition-colors hover:text-maroon-950 dark:text-[#e8c4c7] dark:decoration-[#c59da3] dark:hover:text-white">
                                <span x-text="text('staffAction')">Staff sign in</span>
                                <span class="ml-1" aria-hidden="true">→</span>
                            </a>
                        </div>
                    </aside>
                </section>

                <section aria-labelledby="emergency-title" class="border-y border-[#e8d6d0] bg-[#f1e8e2] dark:border-[#51413e] dark:bg-[#302724]">
                    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                        <div>
                            <h2 id="emergency-title" class="font-sans text-sm font-bold tracking-normal text-[#5e2630] dark:text-[#f0c5c1]" x-text="text('emergencyTitle')">Is someone in immediate danger?</h2>
                            <p class="mt-1 text-sm leading-6 text-[#563f40] dark:text-[#dfcfca]" x-text="text('emergency')">Call 911 for emergencies. CivicGuard reports are not monitored in real time.</p>
                        </div>
                        <a href="tel:911" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-md border border-[#9d5260] px-4 text-sm font-bold text-[#76263a] transition-colors hover:bg-white/50 dark:border-[#a76b76] dark:text-[#ffd6d7] dark:hover:bg-white/5" x-text="text('call911')">Call 911</a>
                    </div>
                </section>

                <section id="how-it-works" class="mx-auto grid max-w-7xl gap-9 px-4 py-14 sm:px-6 sm:py-18 lg:grid-cols-[0.75fr_1.25fr] lg:gap-20 lg:px-8">
                    <div class="max-w-md">
                        <p class="cg-kicker" x-text="text('processIntro')">For residents</p>
                        <h2 class="mt-3 text-3xl leading-tight sm:text-4xl" x-text="text('howHeading')">What happens after you report?</h2>
                        <p class="mt-4 text-sm leading-6 text-stone-700 dark:text-gray-300" x-text="text('howDescription')">You can follow each report from your account. Here is what to expect.</p>
                    </div>
                    <ol>
                        <li class="grid gap-2 border-b border-stone-300 py-5 first:border-t dark:border-[#454440] sm:grid-cols-[3rem_1fr] sm:gap-4 sm:py-6" data-reveal="step">
                            <span class="font-serif text-xl text-maroon-800 dark:text-[#d5aaa9]">1</span>
                            <div>
                                <h3 class="text-base font-semibold" x-text="text('reportStep')">Create a resident account</h3>
                                <p class="mt-1.5 max-w-xl text-sm leading-6 text-stone-700 dark:text-gray-300" x-text="text('reportDescription')">Sign up to send a report and see its updates.</p>
                            </div>
                        </li>
                        <li class="grid gap-2 border-b border-stone-300 py-5 dark:border-[#454440] sm:grid-cols-[3rem_1fr] sm:gap-4 sm:py-6" data-reveal="step">
                            <span class="font-serif text-xl text-maroon-800 dark:text-[#d5aaa9]">2</span>
                            <div>
                                <h3 class="text-base font-semibold" x-text="text('staffStep')">Tell us what happened</h3>
                                <p class="mt-1.5 max-w-xl text-sm leading-6 text-stone-700 dark:text-gray-300" x-text="text('staffDescription')">Choose a category, describe the issue, and enter its location. Adding a photo is optional.</p>
                            </div>
                        </li>
                        <li class="grid gap-2 border-b border-stone-300 py-5 dark:border-[#454440] sm:grid-cols-[3rem_1fr] sm:gap-4 sm:py-6" data-reveal="step">
                            <span class="font-serif text-xl text-maroon-800 dark:text-[#d5aaa9]">3</span>
                            <div>
                                <h3 class="text-base font-semibold" x-text="text('progressStep')">Check the report status</h3>
                                <p class="mt-1.5 max-w-xl text-sm leading-6 text-stone-700 dark:text-gray-300" x-text="text('progressDescription')">Barangay staff review the report and post updates for you to follow.</p>
                            </div>
                        </li>
                    </ol>
                </section>
            </main>

            <footer class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-7 text-sm text-stone-600 dark:text-gray-400 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p class="font-medium text-stone-700 dark:text-gray-300" x-text="text('footerBrand')">CivicGuard · Barangay Maimpis Incident Management</p>
                <p x-text="text('footerEmergency')">For emergencies, call 911.</p>
            </footer>
        </div>
    </body>
</html>
