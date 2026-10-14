<!DOCTYPE html>
<html lang="en" x-data="landingPage" :lang="language">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f6f2eb">
        <meta name="description" content="CivicGuard helps Barangay Maimpis residents report incidents and follow updates from barangay staff.">
        <title>{{ config('app.name', 'CivicGuard') }}</title>
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
            <header class="cg-public-header">
                <nav aria-label="Main navigation" :aria-label="text('navigation')" class="mx-auto flex min-h-[72px] max-w-7xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                    <a href="{{ url('/') }}" class="group flex min-w-0 items-center gap-2.5 rounded-md focus-visible:outline-offset-4" aria-label="CivicGuard, Barangay Maimpis home" :aria-label="text('homeLink')">
                        <span class="cg-brand-mark"><x-application-logo class="h-9 w-9 shrink-0" /></span>
                        <span class="min-w-0">
                            <span class="block text-[15px] font-bold leading-tight tracking-tight text-maroon-900">CivicGuard</span>
                            <span class="block text-[11px] uppercase tracking-wider text-stone-500">Brgy. Maimpis</span>
                        </span>
                    </a>

                    <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                        <button type="button"
                                @click="toggleLanguage()"
                                :aria-label="language === 'en' ? 'Switch language to Tagalog' : 'Switch language to English'"
                                :aria-pressed="(language === 'tl').toString()"
                                class="cg-language-toggle inline-flex min-h-10 min-w-10 items-center justify-center rounded-md border border-stone-300 px-2.5 text-xs font-semibold tracking-wide text-stone-700 transition-colors hover:bg-white/70">
                            <span x-text="language === 'en' ? 'EN' : 'TL'"></span>
                        </button>
                        @guest
                            <a href="{{ route('login') }}" class="inline-flex min-h-10 items-center rounded-md px-2.5 text-sm font-semibold text-maroon-800 transition-colors hover:bg-white/70 hover:text-maroon-950" x-text="text('navSignIn')">Sign in</a>
                            <span class="hidden sm:inline-flex">
                                <a href="{{ route('register') }}" class="cg-btn min-h-10 px-4 text-sm">
                                    <span x-text="text('navCreateAccount')">Create account</span>
                                </a>
                            </span>
                        @endguest
                        @auth
                            <a href="{{ route('dashboard') }}" class="cg-btn ml-1 min-h-10 px-3 text-xs sm:px-4 sm:text-sm">
                                <span x-text="text('dashboard')">Go to your dashboard</span>
                            </a>
                        @endauth
                    </div>
                </nav>
            </header>

            <main id="main-content" class="cg-page-enter">
                <section class="cg-landing-hero lg:flex lg:min-h-[calc(100dvh_-_73px)] lg:items-center">
                    <div class="cg-landing-hero-inner mx-auto grid w-full max-w-7xl gap-10 px-4 py-12 sm:px-6 sm:py-16 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:gap-16 lg:px-8 lg:py-20">
                        <div class="cg-landing-copy">
                            <p class="cg-landing-location cg-intro-enter" x-text="text('processIntro')">For residents</p>
                            <h1 class="cg-intro-enter cg-intro-enter-1 max-w-none text-[2.4rem] leading-[1.04] max-[360px]:text-[2.1rem] sm:max-w-[15ch] sm:text-6xl lg:max-w-none lg:text-[4.25rem]">
                                <span x-text="text('headline')">Report an issue in</span>
                                <span class="cg-landing-accent" x-text="text('headlineAccent')"> Barangay Maimpis.</span>
                            </h1>
                            <p class="cg-intro-enter cg-intro-enter-2 mt-6 max-w-xl text-base leading-7 text-white/85 sm:text-lg sm:leading-8 lg:text-xl lg:leading-9" x-text="text('introduction')">Report an incident, then follow its progress here. CivicGuard connects residents with barangay staff.</p>
                            <div class="cg-intro-enter cg-intro-enter-3 mt-8 flex flex-wrap items-center gap-x-6 gap-y-4">
                                @guest
                                    <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-md bg-white px-6 text-base lg:min-h-14 lg:px-8 lg:text-lg font-semibold text-maroon-900 shadow-sm transition-colors hover:bg-stone-100">
                                        <span x-text="text('residentAction')">Create a resident account</span>
                                        <span class="cg-arrow" aria-hidden="true">→</span>
                                    </a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-md bg-white px-6 text-base lg:min-h-14 lg:px-8 lg:text-lg font-semibold text-maroon-900 shadow-sm transition-colors hover:bg-stone-100">
                                        <span x-text="text('dashboard')">Go to your dashboard</span>
                                        <span class="cg-arrow" aria-hidden="true">→</span>
                                    </a>
                                @endguest
                                <a href="#how-it-works" class="cg-landing-more inline-flex min-h-11 items-center gap-2 rounded-md text-sm font-semibold" x-text="text('howItWorks')">See how it works</a>
                            </div>
                            @guest
                                <p class="cg-intro-enter cg-intro-enter-3 mt-4 text-sm text-white/80">
                                    <a href="{{ route('login') }}" class="underline decoration-white/50 underline-offset-4 hover:text-white" x-text="text('staffQuestion')">Barangay staff? Sign in</a>
                                </p>
                            @endguest
                            <p class="cg-intro-enter cg-intro-enter-3 mt-6 text-sm text-white/75">
                                <span x-text="text('emergencyTitle')">Is someone in immediate danger?</span>
                                <a href="tel:911" class="ml-1 font-semibold text-white underline decoration-white/50 underline-offset-4 hover:decoration-white" x-text="text('call911')">Call 911</a>
                            </p>
                        </div>

                        <aside id="get-started" aria-label="Sample report" :aria-label="text('sampleLabel')" class="cg-start-panel cg-intro-enter cg-intro-enter-3">
                            <div class="cg-start-panel-heading">
                                <p class="cg-start-note" x-text="text('sampleLabel')">Sample report</p>
                            </div>
                            <div class="px-6 pb-7">
                                <p class="text-sm leading-6 text-stone-700 lg:text-base lg:leading-7" x-text="text('sampleIntro')">After you send a report, you can follow its progress like this.</p>
                                <div class="mt-5 rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-base font-semibold text-stone-950 lg:text-lg" x-text="text('sampleTitle')">Broken streetlight</p>
                                            <p class="mt-0.5 text-sm text-stone-600" x-text="text('sampleSent')">Sent today</p>
                                        </div>
                                        <span class="shrink-0 rounded-full bg-maroon-700 px-3 py-1 text-xs font-semibold text-white" x-text="text('tlProgress')">In progress</span>
                                    </div>
                                    <ol class="mt-6 grid grid-cols-3 gap-2">
                                        <li>
                                            <span class="block h-1.5 rounded-full bg-maroon-700"></span>
                                            <span class="mt-2 block text-xs font-semibold text-stone-900 sm:text-sm" x-text="text('tlReceived')">Received</span>
                                        </li>
                                        <li>
                                            <span class="block h-1.5 rounded-full bg-maroon-700"></span>
                                            <span class="mt-2 block text-xs font-semibold text-maroon-800 sm:text-sm" x-text="text('tlProgress')">In progress</span>
                                        </li>
                                        <li>
                                            <span class="block h-1.5 rounded-full bg-stone-300"></span>
                                            <span class="mt-2 block text-xs text-stone-500 sm:text-sm" x-text="text('tlResolved')">Resolved</span>
                                        </li>
                                    </ol>
                                </div>
                                <p class="mt-5 text-sm leading-6 text-stone-700 lg:text-base lg:leading-7" x-text="text('sampleFoot')">You can check this anytime from your account.</p>
                            </div>
                        </aside>
                    </div>
                </section>

                <section aria-labelledby="emergency-title" class="cg-emergency-strip" data-cg-scroll>
                    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                        <div>
                            <h2 id="emergency-title" class="font-sans text-sm font-bold tracking-normal text-[#5e2630]" x-text="text('emergencyTitle')">Is someone in immediate danger?</h2>
                            <p class="mt-1 text-sm leading-6 text-[#563f40]" x-text="text('emergency')">Call 911 for emergencies. CivicGuard reports are not monitored in real time.</p>
                        </div>
                        <a href="tel:911" class="cg-emergency-action" x-text="text('call911')">Call 911</a>
                    </div>
                </section>

                <section id="how-it-works" class="cg-how-section mx-auto grid max-w-7xl gap-9 px-4 py-14 sm:px-6 sm:py-18 lg:grid-cols-[0.75fr_1.25fr] lg:gap-20 lg:px-8 lg:py-24">
                    <div class="max-w-md" data-cg-scroll>
                        <p class="cg-kicker" x-text="text('processIntro')">For residents</p>
                        <h2 class="mt-3 text-3xl leading-tight sm:text-4xl" x-text="text('howHeading')">What happens after you report?</h2>
                        <p class="mt-4 text-sm leading-6 text-stone-700" x-text="text('howDescription')">You can follow each report from your account. Here is what to expect.</p>
                    </div>
                    <ol class="cg-how-steps">
                        <li class="cg-how-step grid gap-2 py-5 sm:grid-cols-[3rem_1fr] sm:gap-4 sm:py-6" data-cg-scroll>
                            <span class="cg-how-index">1</span>
                            <div>
                                <h3 class="text-base font-semibold" x-text="text('reportStep')">Create a resident account</h3>
                                <p class="mt-1.5 max-w-xl text-sm leading-6 text-stone-700" x-text="text('reportDescription')">Sign up to send a report and see its updates.</p>
                            </div>
                        </li>
                        <li class="cg-how-step grid gap-2 py-5 sm:grid-cols-[3rem_1fr] sm:gap-4 sm:py-6" data-cg-scroll>
                            <span class="cg-how-index">2</span>
                            <div>
                                <h3 class="text-base font-semibold" x-text="text('staffStep')">Tell us what happened</h3>
                                <p class="mt-1.5 max-w-xl text-sm leading-6 text-stone-700" x-text="text('staffDescription')">Choose a category, describe the issue, and enter its location. Adding a photo is optional.</p>
                            </div>
                        </li>
                        <li class="cg-how-step grid gap-2 py-5 sm:grid-cols-[3rem_1fr] sm:gap-4 sm:py-6" data-cg-scroll>
                            <span class="cg-how-index">3</span>
                            <div>
                                <h3 class="text-base font-semibold" x-text="text('progressStep')">Check the report status</h3>
                                <p class="mt-1.5 max-w-xl text-sm leading-6 text-stone-700" x-text="text('progressDescription')">Barangay staff review the report and post updates for you to follow.</p>
                            </div>
                        </li>
                    </ol>
                </section>
            </main>

            <footer class="cg-public-footer mx-auto flex max-w-7xl flex-col gap-3 px-4 py-7 text-sm text-stone-600 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p class="font-medium text-stone-700" x-text="text('footerBrand')">CivicGuard · Barangay Maimpis Incident and Curfew Reporting System</p>
                <p x-text="text('footerEmergency')">For emergencies, call 911.</p>
                <p class="text-xs text-stone-500 sm:basis-full"><span class="font-medium" x-text="text('devKicker')">Developed by</span> Mico Gerard Navarro, Franz Mikey Reyes, Carl Spencer Talon, Adriann Enriquez</p>
            </footer>
        </div>
    </body>
</html>
