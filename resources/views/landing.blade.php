<!DOCTYPE html>
<html lang="en" x-data="landingPage" :lang="language">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
        @include('partials.pwa-head')
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
                        <button type="button" x-cloak x-show="!installed" @click="showGuide()" :aria-label="text('installNav')" class="cg-language-toggle inline-flex min-h-10 items-center justify-center gap-1.5 rounded-md border border-stone-300 px-2.5 text-xs font-semibold tracking-wide text-stone-700 transition-colors hover:bg-white/70"><svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v11m0 0l-4-4m4 4l4-4M5 19h14"/></svg><span class="hidden sm:inline" x-text="text('installNav')">Install app</span></button>
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
                            <div class="cg-hero-head"><h1 class="cg-intro-enter cg-intro-enter-1 cg-hero-title">
                                <span x-text="text('headline')">Report an issue in</span>
                                <span class="cg-landing-accent" x-text="text('headlineAccent')"> Barangay Maimpis.</span>
                            </h1><button type="button" x-cloak x-show="!installed" @click="showGuide()" :aria-label="text('installHint')" class="cg-hero-phone"><span class="cg-hp-body" aria-hidden="true"><span class="cg-hp-island"></span><span class="cg-hp-grid"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><img class="cg-hp-icon" src="/icons/icon-192.png" alt=""></span></span><span class="cg-hp-hint" x-text="text('installHint')">Add to your home screen</span></button></div>
                            <p class="cg-intro-enter cg-intro-enter-2 mt-6 max-w-xl text-base leading-7 text-white/85 sm:text-lg sm:leading-8 lg:text-xl lg:leading-9" x-text="text('introduction')">Report an incident, then follow its progress here. CivicGuard connects residents with barangay staff.</p>
                            <div class="cg-intro-enter cg-intro-enter-3 mt-9 flex flex-col items-stretch gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:gap-x-4 sm:gap-y-3">
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
                                <button type="button" x-cloak x-show="!installed" @click="showGuide()" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-md bg-white px-6 text-base font-semibold text-maroon-900 shadow-sm transition-colors hover:bg-stone-100 lg:min-h-14 lg:px-8 lg:text-lg"><svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v11m0 0l-4-4m4 4l4-4M5 19h14"/></svg><span x-text="text('installNav')">Install app</span></button>
                                <div class="mt-1 sm:basis-full"><a href="#how-it-works" class="cg-landing-more group inline-flex min-h-11 items-center gap-2 rounded-md text-sm font-semibold"><span x-text="text('howItWorks')">See how it works</span><svg class="h-4 w-4 transition-transform group-hover:translate-y-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/></svg></a></div>
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
                            <div class="px-6 py-7">
                                <p class="cg-sample-text text-sm leading-6 text-stone-700 lg:text-base lg:leading-7" x-text="text('sampleIntro')">After you send a report, you can follow its progress like this.</p>
                                <div class="mt-5 rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-base font-semibold text-stone-950 lg:text-lg" x-text="text('sampleTitle')">Broken streetlight</p>
                                            <p class="mt-0.5 text-sm text-stone-600" x-text="text('sampleSent')">Sent today</p>
                                        </div>
                                        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-maroon-700 px-3 py-1 text-xs font-semibold text-white"><span class="relative flex h-2 w-2" aria-hidden="true"><span class="cg-ping-ring absolute inline-flex h-full w-full rounded-full bg-white/70"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-white"></span></span><span x-text="text('tlProgress')">In progress</span></span>
                                    </div>
                                    <ol class="mt-6 grid grid-cols-3 gap-2">
                                        <li>
                                            <span class="block h-1.5 overflow-hidden rounded-full bg-stone-300"><span class="cg-fill cg-fill-1 block h-full w-full rounded-full bg-maroon-700"></span></span>
                                            <span class="cg-label cg-label-1 mt-2 block text-xs font-semibold text-stone-900 sm:text-sm" x-text="text('tlReceived')">Received</span>
                                        </li>
                                        <li>
                                            <span class="block h-1.5 overflow-hidden rounded-full bg-stone-300"><span class="cg-fill cg-fill-2 block h-full w-full rounded-full bg-maroon-700"></span></span>
                                            <span class="cg-label cg-label-2 mt-2 block text-xs font-semibold text-maroon-800 sm:text-sm" x-text="text('tlProgress')">In progress</span>
                                        </li>
                                        <li>
                                            <span class="cg-pending block h-1.5 rounded-full bg-stone-300"></span>
                                            <span class="cg-label cg-label-3 mt-2 block text-xs text-stone-500 sm:text-sm" x-text="text('tlResolved')">Resolved</span>
                                        </li>
                                    </ol>
                                </div>
                                <p class="cg-sample-foot mt-5 text-sm leading-6 text-stone-700 lg:text-base lg:leading-7" x-text="text('sampleFoot')">You can check this anytime from your account.</p>
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

                <section aria-labelledby="features-title" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
                    <div class="max-w-2xl" data-cg-scroll>
                        <p class="cg-kicker" x-text="text('featKicker')">Why CivicGuard</p>
                        <h2 id="features-title" class="mt-3 text-3xl leading-tight sm:text-4xl" x-text="text('featHeading')">Reporting made simple</h2>
                    </div>
                    <ul class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <li class="rounded-xl border border-stone-200 bg-white p-6" data-cg-scroll>
                                <h3 class="text-base font-semibold text-stone-950" x-text="text('feat1Title')">Add a photo and location</h3>
                                <p class="mt-2 text-sm leading-6 text-stone-700" x-text="text('feat1Text')">Attach a photo and drop a pin so staff can find the problem.</p>
                            </li>
                            <li class="rounded-xl border border-stone-200 bg-white p-6" data-cg-scroll>
                                <h3 class="text-base font-semibold text-stone-950" x-text="text('feat2Title')">Follow every update</h3>
                                <p class="mt-2 text-sm leading-6 text-stone-700" x-text="text('feat2Text')">See when your report is received, assigned, in progress, and resolved.</p>
                            </li>
                            <li class="rounded-xl border border-stone-200 bg-white p-6" data-cg-scroll>
                                <h3 class="text-base font-semibold text-stone-950" x-text="text('feat3Title')">Get notified</h3>
                                <p class="mt-2 text-sm leading-6 text-stone-700" x-text="text('feat3Text')">Receive a notification when the status of your report changes.</p>
                            </li>
                            <li class="rounded-xl border border-stone-200 bg-white p-6" data-cg-scroll>
                                <h3 class="text-base font-semibold text-stone-950" x-text="text('feat4Title')">English and Tagalog</h3>
                                <p class="mt-2 text-sm leading-6 text-stone-700" x-text="text('feat4Text')">Use the site in the language you are most comfortable with.</p>
                            </li>
                    </ul>
                </section>

                <section aria-labelledby="staff-title" class="bg-maroon-900 text-white">
                    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-14 sm:px-6 lg:grid-cols-[0.75fr_1.25fr] lg:gap-20 lg:px-8 lg:py-20">
                        <div class="max-w-md" data-cg-scroll>
                            <p class="text-xs font-semibold uppercase tracking-widest text-white/70" x-text="text('staffKicker')">For barangay staff</p>
                            <h2 id="staff-title" class="mt-3 text-3xl leading-tight text-white sm:text-4xl" x-text="text('staffHeading')">One place for every report</h2>
                        </div>
                        <ul class="divide-y divide-white/20 border-y border-white/20">
                            <li class="py-5 text-base text-white/90" data-cg-scroll x-text="text('staff1')">Review reports sorted by urgency</li>
                            <li class="py-5 text-base text-white/90" data-cg-scroll x-text="text('staff2')">Assign reports and post updates for residents</li>
                            <li class="py-5 text-base text-white/90" data-cg-scroll x-text="text('staff3')">Log curfew violations (for tanods)</li>
                        </ul>
                    </div>
                </section>

                <section aria-labelledby="faq-title" class="mx-auto max-w-3xl px-4 py-14 sm:px-6 lg:py-20">
                    <h2 id="faq-title" class="text-3xl leading-tight sm:text-4xl" data-cg-scroll x-text="text('faqHeading')">Common questions</h2>
                    <div class="mt-8 border-t border-stone-300/70">
                        <details class="group border-b border-stone-300/70 py-4" data-cg-scroll>
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-base font-semibold text-stone-900 [&::-webkit-details-marker]:hidden">
                                <span x-text="text('faq1Q')">Do I need an account to report?</span>
                                <span class="text-xl leading-none text-maroon-700 transition-transform group-open:rotate-45" aria-hidden="true">+</span>
                            </summary>
                            <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-700" x-text="text('faq1A')">Yes. Create a resident account so you can send a report and follow its updates.</p>
                        </details>
                        <details class="group border-b border-stone-300/70 py-4" data-cg-scroll>
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-base font-semibold text-stone-900 [&::-webkit-details-marker]:hidden">
                                <span x-text="text('faq2Q')">Can I use CivicGuard in an emergency?</span>
                                <span class="text-xl leading-none text-maroon-700 transition-transform group-open:rotate-45" aria-hidden="true">+</span>
                            </summary>
                            <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-700" x-text="text('faq2A')">No. Call 911 first. CivicGuard reports are not monitored in real time.</p>
                        </details>
                        <details class="group border-b border-stone-300/70 py-4" data-cg-scroll>
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-base font-semibold text-stone-900 [&::-webkit-details-marker]:hidden">
                                <span x-text="text('faq3Q')">What if my issue gets fixed?</span>
                                <span class="text-xl leading-none text-maroon-700 transition-transform group-open:rotate-45" aria-hidden="true">+</span>
                            </summary>
                            <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-700" x-text="text('faq3A')">When a report is marked resolved, you can confirm it or reopen it within 7 days.</p>
                        </details>
                        <details class="group border-b border-stone-300/70 py-4" data-cg-scroll>
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-base font-semibold text-stone-900 [&::-webkit-details-marker]:hidden">
                                <span x-text="text('faq4Q')">Is a photo required?</span>
                                <span class="text-xl leading-none text-maroon-700 transition-transform group-open:rotate-45" aria-hidden="true">+</span>
                            </summary>
                            <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-700" x-text="text('faq4A')">No. A photo is optional, but it helps staff understand the issue.</p>
                        </details>
                    </div>
                </section>

                <section id="install-guide" aria-labelledby="install-title" class="cg-install mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20"><div class="mx-auto max-w-2xl text-center" data-cg-scroll><p class="cg-kicker" x-text="text('installKicker')">Install the app</p><h2 id="install-title" class="mt-3 text-3xl leading-tight sm:text-4xl" x-text="text('installHeading')">Put CivicGuard on your home screen</h2><p class="mt-4 text-sm leading-6 text-stone-700" x-text="text('installIntro')">No app store needed. It takes about 20 seconds.</p></div><div class="mt-8 flex justify-center"><div class="cg-install-tabs inline-flex gap-1 rounded-lg border border-stone-300 bg-white p-1" role="tablist"><button type="button" role="tab" :aria-selected="(guideTab === 'ios').toString()" @click="guideTab = 'ios'" class="min-h-10 rounded-md px-5 text-sm font-semibold text-stone-700 transition-colors" x-text="text('tabIos')">iPhone</button><button type="button" role="tab" :aria-selected="(guideTab === 'android').toString()" @click="guideTab = 'android'" class="min-h-10 rounded-md px-5 text-sm font-semibold text-stone-700 transition-colors" x-text="text('tabAndroid')">Android</button></div></div><div x-show="guideOn" class="mt-8"><div x-show="guideTab === 'ios'" x-cloak role="tabpanel" class="mx-auto grid max-w-4xl items-center justify-center gap-10 lg:grid-cols-[auto_minmax(0,26rem)] lg:gap-16"><div class="cg-phone-wrap"><div class="cg-phone" aria-hidden="true"><div class="cg-phone-screen"><span class="cg-phone-island"></span><div class="cg-ph-status"><span>9:41</span><span>5G</span></div><div class="cg-ph-layer cg-ph-pagelayer"><div class="cg-ph-hero"><b>Report an issue in Barangay Maimpis.</b><i class="cg-ph-bar" style="width:90%"></i><i class="cg-ph-bar" style="width:68%"></i><span class="cg-ph-cta">Create a resident account</span></div><div class="cg-ph-card"></div><div class="cg-ph-card"></div><div class="cg-ph-toolbar"><svg class="cg-ph-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg><svg class="cg-ph-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg><span class="cg-ph-pos"><svg class="cg-ph-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15V4m0 0l-4 4m4-4l4 4M6 11H5a1 1 0 00-1 1v7a1 1 0 001 1h14a1 1 0 001-1v-7a1 1 0 00-1-1h-1"/></svg><span class="cg-tap" style="animation-delay:0.5s"></span></span><svg class="cg-ph-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h7a2 2 0 012 2v12a2 2 0 00-2-2H4zM20 5h-7"/><path d="M20 5v12h-7"/></svg><svg class="cg-ph-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="12" height="12" rx="2"/><path d="M8 20h10a2 2 0 002-2V8"/></svg></div></div><div class="cg-ph-dim"></div><div class="cg-ph-sheet"><div class="cg-ph-sheethead"><img src="/icons/icon-192.png" alt=""><div><b>CivicGuard</b><small>civicguard.wasmer.app</small></div></div><div class="cg-ph-rows"><div class="cg-ph-row"><span>Copy</span></div><div class="cg-ph-row"><span>Add to Bookmarks</span></div><div class="cg-ph-row is-key"><span>Add to Home Screen</span><span class="cg-ph-glyph">+</span><span class="cg-tap" style="animation-delay:2.1s;left:80%"></span></div><div class="cg-ph-row"><span>Find on Page</span></div></div></div><div class="cg-ph-dialog"><div class="cg-ph-dialoghead"><span class="cg-ph-blue">Cancel</span><b>Add to Home Screen</b><span class="cg-ph-blue cg-ph-pos"><b>Add</b><span class="cg-tap" style="animation-delay:3.6s"></span></span></div><div class="cg-ph-dialogbody"><img src="/icons/icon-192.png" alt=""><div><b>CivicGuard</b><small>civicguard.wasmer.app</small></div></div></div><div class="cg-ph-home"><div class="cg-ph-grid"><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><span class="cg-ph-appwrap"><img class="cg-ph-appicon" src="/icons/icon-192.png" alt=""><em>CivicGuard</em></span></div><div class="cg-ph-dock"></div></div></div></div></div><div class="max-w-xl"><ol class="space-y-2"><li class="cg-install-step" style="animation-delay:0s"><span class="cg-install-num">1</span><div><h3 class="text-base font-semibold text-stone-950" x-text="text('iosStep1')"></h3><p class="mt-1 text-sm leading-6 text-stone-700" x-text="text('iosStep1d')"></p></div></li><li class="cg-install-step" style="animation-delay:2s"><span class="cg-install-num">2</span><div><h3 class="text-base font-semibold text-stone-950" x-text="text('iosStep2')"></h3><p class="mt-1 text-sm leading-6 text-stone-700" x-text="text('iosStep2d')"></p></div></li><li class="cg-install-step cg-install-step-last" style="animation-delay:3.6s"><span class="cg-install-num">3</span><div><h3 class="text-base font-semibold text-stone-950" x-text="text('iosStep3')"></h3><p class="mt-1 text-sm leading-6 text-stone-700" x-text="text('iosStep3d')"></p></div></li></ol><p class="mt-5 text-sm leading-6 text-stone-600" x-text="text('iosNote')"></p></div></div><div x-show="guideTab === 'android'" x-cloak role="tabpanel" class="mx-auto grid max-w-4xl items-center justify-center gap-10 lg:grid-cols-[auto_minmax(0,26rem)] lg:gap-16"><div class="cg-phone-wrap"><div class="cg-phone is-android" aria-hidden="true"><div class="cg-phone-screen"><span class="cg-phone-punch"></span><div class="cg-ph-status"><span>9:41</span><span>5G</span></div><div class="cg-ph-layer cg-ph-pagelayer"><div class="cg-ph-urlbar"><span class="cg-ph-url">civicguard.wasmer.app</span><span class="cg-ph-dots cg-ph-pos"><svg class="cg-ph-ico" viewBox="0 0 24 24" fill="#444"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg><span class="cg-tap" style="animation-delay:0.5s"></span></span></div><div class="cg-ph-hero"><b>Report an issue in Barangay Maimpis.</b><i class="cg-ph-bar" style="width:90%"></i><i class="cg-ph-bar" style="width:68%"></i><span class="cg-ph-cta">Create a resident account</span></div><div class="cg-ph-card"></div><div class="cg-ph-card"></div></div><div class="cg-ph-dim"></div><div class="cg-ph-menu"><span>New tab</span><span>History</span><span class="is-key">Install app<span class="cg-tap" style="animation-delay:2.0s;left:50%"></span></span><span>Settings</span></div><div class="cg-ph-dialog"><div class="cg-ph-dialogbody"><img src="/icons/icon-192.png" alt=""><div><b>Install app?</b><small>CivicGuard</small></div></div><div class="cg-ph-btns"><span class="cg-ph-blue">Cancel</span><span class="cg-ph-blue cg-ph-pos"><b>Install</b><span class="cg-tap" style="animation-delay:3.6s"></span></span></div></div><div class="cg-ph-home"><div class="cg-ph-grid"><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><i class="cg-ph-app"></i><span class="cg-ph-appwrap"><img class="cg-ph-appicon" src="/icons/icon-192.png" alt=""><em>CivicGuard</em></span></div><div class="cg-ph-dock"></div></div></div></div></div><div class="max-w-xl"><ol class="space-y-2"><li class="cg-install-step" style="animation-delay:0s"><span class="cg-install-num">1</span><div><h3 class="text-base font-semibold text-stone-950" x-text="text('andStep1')"></h3><p class="mt-1 text-sm leading-6 text-stone-700" x-text="text('andStep1d')"></p></div></li><li class="cg-install-step" style="animation-delay:2s"><span class="cg-install-num">2</span><div><h3 class="text-base font-semibold text-stone-950" x-text="text('andStep2')"></h3><p class="mt-1 text-sm leading-6 text-stone-700" x-text="text('andStep2d')"></p></div></li><li class="cg-install-step cg-install-step-last" style="animation-delay:3.6s"><span class="cg-install-num">3</span><div><h3 class="text-base font-semibold text-stone-950" x-text="text('andStep3')"></h3><p class="mt-1 text-sm leading-6 text-stone-700" x-text="text('andStep3d')"></p></div></li></ol><p class="mt-5 text-sm leading-6 text-stone-600" x-text="text('andNote')"></p><button type="button" x-show="canPrompt" x-cloak @click="nativeInstall()" class="cg-btn mt-5" x-text="text('installNow')">Install now</button></div></div></div></section>

                <section aria-labelledby="cta-title" class="bg-maroon-900 text-white">
                    <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-14 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8 lg:py-16">
                        <div class="max-w-xl" data-cg-scroll>
                            <h2 id="cta-title" class="text-3xl leading-tight text-white sm:text-4xl" x-text="text('ctaHeading')">Ready to report an issue?</h2>
                            <p class="mt-3 text-base leading-7 text-white/80" x-text="text('ctaText')">Create an account and follow your report from start to finish.</p>
                        </div>
                        @guest
                            <a href="{{ route('register') }}" class="inline-flex min-h-12 shrink-0 items-center justify-center gap-2 rounded-md bg-white px-6 text-base font-semibold text-maroon-900 shadow-sm transition-colors hover:bg-stone-100" data-cg-scroll>
                                <span x-text="text('residentAction')">Create a resident account</span>
                                <span class="cg-arrow" aria-hidden="true">→</span>
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="inline-flex min-h-12 shrink-0 items-center justify-center gap-2 rounded-md bg-white px-6 text-base font-semibold text-maroon-900 shadow-sm transition-colors hover:bg-stone-100" data-cg-scroll>
                                <span x-text="text('dashboard')">Go to your dashboard</span>
                                <span class="cg-arrow" aria-hidden="true">→</span>
                            </a>
                        @endguest
                    </div>
                </section>
            </main>

            <footer class="cg-public-footer mx-auto flex max-w-7xl flex-col gap-3 px-4 py-7 text-sm text-stone-600 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p class="font-medium text-stone-700" x-text="text('footerBrand')">CivicGuard · Barangay Maimpis · Incident and Curfew Reporting System</p>
                <p x-text="text('footerEmergency')">For emergencies, call 911.</p>
                <p class="flex flex-wrap gap-x-5">
                    <a href="{{ route('privacy') }}" class="inline-block py-2 underline underline-offset-4 hover:text-stone-900" x-text="text('footerPrivacy')">Privacy Policy</a>
                    <a href="{{ route('terms') }}" class="inline-block py-2 underline underline-offset-4 hover:text-stone-900" x-text="text('footerTerms')">Terms of Use</a>
                </p>
                <p class="mt-2 border-t border-stone-300/70 pt-4 text-sm font-medium text-stone-800 sm:basis-full"><span class="font-semibold text-maroon-800" x-text="text('devKicker')">Developed by</span> Mico Gerard Navarro, Franz Mikey Reyes, Carl Spencer Talon, Adriann Enriquez</p>
            </footer>
        </div>
    </body>
</html>
