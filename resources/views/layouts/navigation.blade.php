<div x-data="{ open: false }"
     x-effect="document.body.classList.toggle('overflow-hidden', open)"
     @keydown.escape.window="open = false"
     @resize.window="if (window.innerWidth >= 1024) open = false"
     class="lg:h-full">
    <!-- Mobile top bar -->
    <div class="lg:hidden flex items-center gap-2 h-16 px-3 border-b border-stone-300 dark:border-stone-800/50 bg-[#ebe5d8] dark:bg-[#121212]">
        <button @click="open = true" aria-label="Open menu" class="p-2.5 rounded-md text-stone-600 dark:text-gray-400 hover:bg-black/5 dark:hover:bg-white/5">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <x-application-logo class="h-8 w-auto text-maroon-700" />
            <span class="font-bold text-sm text-maroon-800 dark:text-gray-100">CivicGuard</span>
        </a>
    </div>

    <!-- Mobile overlay -->
    <div x-show="open" x-cloak x-transition.opacity @click="open = false" class="fixed inset-0 bg-black/40 z-40 lg:hidden"></div>

    <!-- Sidebar: hidden on mobile until opened, always visible on desktop -->
    <aside
        x-show="open"
        x-cloak
        x-transition:enter="transition-transform duration-200 ease-out"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition-transform duration-150 ease-in"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] flex flex-col border-r-0/50 bg-[#ebe5d8] dark:bg-[#121212] lg:!flex lg:w-64 lg:max-w-none lg:sticky lg:top-0 lg:h-screen"
    >
        <!-- Brand -->
        <div class="h-16 flex items-center justify-between px-5 shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <x-application-logo class="h-8 w-auto text-maroon-700" />
                <span class="leading-tight">
                    <span class="block font-sans font-bold text-sm text-maroon-800 dark:text-gray-100">CivicGuard</span>
                    <span class="block text-[10px] uppercase tracking-wider text-stone-500 dark:text-gray-500">Brgy. Maimpis</span>
                </span>
            </a>
            <button @click="open = false" aria-label="Close menu" class="lg:hidden p-2 -mr-2 text-stone-400 hover:text-stone-600 dark:hover:text-gray-200">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Nav links -->
        <nav class="flex-1 overflow-y-auto overscroll-contain px-3 py-3 space-y-0.5">
            @if (Auth::user()->role === 'resident')
                <x-sidebar-link icon="plus" :href="route('reports.create')" :active="request()->routeIs('reports.create')">
                    {{ __('Report Incident') }}
                </x-sidebar-link>
                <x-sidebar-link icon="list" :href="route('reports.index')" :active="request()->routeIs('reports.index')">
                    {{ __('My Reports') }}
                </x-sidebar-link>
            @endif

            @if (in_array(Auth::user()->role, ['tanod', 'admin', 'official']))
                <x-sidebar-link icon="list" :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('All Reports') }}
                </x-sidebar-link>
                <x-sidebar-link icon="chart" :href="route('admin.analytics')" :active="request()->routeIs('admin.analytics')">
                    {{ __('Analytics') }}
                </x-sidebar-link>
                <x-sidebar-link icon="document" :href="route('admin.consolidatedReports')" :active="request()->routeIs('admin.consolidatedReports')">
                    {{ __('Reports') }}
                </x-sidebar-link>
                <x-sidebar-link icon="shield" :href="route('admin.auditLogs')" :active="request()->routeIs('admin.auditLogs')">
                    {{ __('Audit Logs') }}
                </x-sidebar-link>
            @endif

            @if (Auth::user()->role === 'admin')
                <x-sidebar-link icon="user-plus" :href="route('admin.staff.create')" :active="request()->routeIs('admin.staff.create')">
                    {{ __('Add Staff') }}
                </x-sidebar-link>
            @endif

            @if (in_array(Auth::user()->role, ['tanod', 'admin']))
                <x-sidebar-link icon="moon" :href="route('curfew.create')" :active="request()->routeIs('curfew.create')">
                    {{ __('Log Curfew') }}
                </x-sidebar-link>
            @endif

            @if (in_array(Auth::user()->role, ['resident', 'admin', 'official']))
                <x-sidebar-link icon="sparkles" :href="route('chatbot.widget')" :active="request()->routeIs('chatbot.widget')">
                    <span class="font-bold text-maroon-700 dark:text-gray-100">{{ __('AI') }}</span> {{ __('Assistant') }}
                </x-sidebar-link>
            @endif

            <x-sidebar-link icon="bell" :href="route('notifications.index')" :active="request()->routeIs('notifications.index')">
                <span class="flex items-center justify-between">
                    <span>{{ __('Notifications') }}</span>
                    @php $unreadCount = \App\Models\AppNotification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                    @if ($unreadCount > 0)
                        <span class="bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[1rem] h-4 px-1 flex items-center justify-center">{{ $unreadCount }}</span>
                    @endif
                </span>
            </x-sidebar-link>
        </nav>

        <!-- Bottom section -->
        <div class="border-t border-stone-300/70 dark:border-stone-800/50 p-3 space-y-2 shrink-0">
            <div class="flex items-center gap-2">
                <select aria-label="Language" onchange="window.location.href = this.value" class="flex-1 h-10 lg:h-9 rounded-lg border-stone-300 bg-transparent text-xs text-stone-700 dark:border-gray-700 dark:bg-[#121212] dark:text-gray-300">
                    <option value="{{ route('locale.switch', 'en') }}" @selected(app()->getLocale() === 'en')>{{ __('English') }}</option>
                    <option value="{{ route('locale.switch', 'tl') }}" @selected(app()->getLocale() === 'tl')>{{ __('Tagalog') }}</option>
                </select>
                <button @click="darkMode = !darkMode" aria-label="Toggle dark mode" class="flex h-10 w-10 lg:h-9 lg:w-9 shrink-0 items-center justify-center rounded-lg text-stone-500 transition hover:bg-black/5 dark:text-gray-400 dark:hover:bg-white/5">
                    <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>
            </div>

            @php
                $roleLabel = match (Auth::user()->role) {
                    'official' => __('Barangay Official'),
                    'tanod'    => __('Tanod'),
                    'admin'    => __('Admin'),
                    'resident' => __('Resident'),
                    default    => ucfirst(Auth::user()->role),
                };
            @endphp

            <!-- Profile (menu opens upward) -->
            <div x-data="{ menu: false }" @click.outside="menu = false" @keydown.escape.window="menu = false" class="relative">
                <div x-show="menu" x-cloak x-transition.origin.bottom
                     class="absolute bottom-full left-0 right-0 mb-2 rounded-xl border border-stone-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-[#202020]">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-3 lg:py-2 text-sm text-stone-700 hover:bg-stone-100 dark:text-gray-200 dark:hover:bg-white/5">
                        {{ __('Profile') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-3 lg:py-2 text-left text-sm text-stone-700 hover:bg-stone-100 dark:text-gray-200 dark:hover:bg-white/5">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>

                <button @click="menu = !menu" class="w-full flex items-center gap-3 rounded-lg px-2 py-2 text-left hover:bg-black/5 dark:hover:bg-white/5 transition">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-maroon-700 text-sm font-semibold text-white">
                        {{ strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                    </span>
                    <span class="min-w-0 flex-1 leading-tight">
                        <span class="block truncate text-sm font-semibold text-stone-800 dark:text-gray-100">{{ Auth::user()->name }}</span>
                        <span class="block truncate text-xs font-medium text-maroon-700 dark:text-rose-300">{{ $roleLabel }}</span>
                    </span>
                    <svg class="h-4 w-4 shrink-0 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                    </svg>
                </button>
            </div>
        </div>
    </aside>
</div>
