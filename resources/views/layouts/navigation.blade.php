<div x-data="{ open: false }"
     x-effect="document.body.classList.toggle('overflow-hidden', open)"
     @keydown.escape.window="open = false"
     @resize.window="if (window.innerWidth >= 1024) open = false"
     class="lg:h-full">
    <!-- Mobile top bar -->
    <div class="cg-sidebar-mobilebar lg:hidden flex items-center justify-between h-16 px-3 border-b border-stone-200 dark:border-[#3d3d3a]">
        <div class="flex items-center gap-2">
            <button @click="open = true" :aria-expanded="open.toString()" aria-controls="primary-navigation" aria-label="{{ __('Open menu') }}" class="p-2.5 rounded-xl text-stone-700 dark:text-gray-200 hover:bg-black/5 dark:hover:bg-white/5 focus-visible:outline-offset-0">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <x-application-logo class="h-8 w-auto text-maroon-700" />
                <span class="font-bold text-sm text-maroon-800 dark:text-gray-100">CivicGuard</span>
            </a>
        </div>
        <x-notification-dropdown :mobile="true" />
    </div>

    <!-- Mobile overlay -->
    <div x-show="open" x-cloak x-transition.opacity @click="open = false" class="fixed inset-0 bg-stone-950/45 backdrop-blur-[2px] z-40 lg:hidden"></div>

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
        id="primary-navigation"
        :class="collapsed ? 'lg:!w-[76px]' : 'lg:!w-64'"
        class="cg-app-sidebar fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] flex flex-col border-r border-white/10 lg:!flex lg:w-64 lg:max-w-none lg:sticky lg:top-0 lg:h-screen"
    >
        <!-- Brand -->
        <div class="h-16 flex items-center justify-between border-b border-white/15 px-5 shrink-0 cg-sidebar-brand">
            <a href="{{ route('dashboard') }}" aria-label="CivicGuard" class="flex items-center gap-2.5 min-w-0">
                <x-application-logo inverse class="h-8 w-auto" />
                <span class="cg-sidebar-label leading-tight">
                    <span class="block font-sans font-bold text-sm text-white">CivicGuard</span>
                    <span class="block text-[10px] uppercase tracking-wider text-white/70">Brgy. Maimpis</span>
                </span>
            </a>
            <button type="button" @click="toggleSidebar()" :aria-expanded="(!collapsed).toString()" aria-controls="primary-navigation"
                    :aria-label='collapsed ? @js(__('Expand sidebar')) : @js(__('Collapse sidebar'))'
                    :title='collapsed ? @js(__('Expand sidebar')) : @js(__('Collapse sidebar'))'
                    class="hidden lg:inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-white/70 hover:bg-white/10 hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    data-testid="sidebar-toggle">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <rect x="3.5" y="4.5" width="17" height="15" rx="3" stroke-linejoin="round" />
                    <path stroke-linecap="round" d="M9.5 4.5v15" />
                </svg>
            </button>
            <button @click="open = false" aria-label="Close menu" class="lg:hidden p-2 -mr-2 text-white/70 hover:text-white">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Nav links -->
        @php
            $role = Auth::user()->role;
            $isStaff = in_array($role, ['tanod', 'admin', 'official']);
            $groupLabel = 'cg-sidebar-label px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-white/60';
        @endphp
        <nav class="flex-1 overflow-y-auto overscroll-contain px-3 py-4 space-y-1" aria-label="{{ __('Main menu') }}">
            <x-sidebar-link icon="home" :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Home') }}
            </x-sidebar-link>

            @if ($role === 'resident')
                <x-sidebar-link icon="plus" :href="route('reports.create')" :active="request()->routeIs('reports.create')">
                    {{ __('Report Incident') }}
                </x-sidebar-link>
                <x-sidebar-link icon="list" :href="route('reports.index')" :active="request()->routeIs('reports.index')">
                    {{ __('My Reports') }}
                </x-sidebar-link>
            @endif

            @if ($isStaff)
                <p class="{{ $groupLabel }} pt-4">{{ __('Incidents') }}</p>
                <x-sidebar-link icon="list" :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard', 'admin.reports.details', 'admin.reports.curfewDetails')">
                    {{ __('Incident Queue') }}
                </x-sidebar-link>
                <x-sidebar-link icon="chart" :href="route('admin.analytics')" :active="request()->routeIs('admin.analytics', 'admin.consolidatedReports')">
                    {{ __('Reports') }}
                </x-sidebar-link>
                @if (in_array($role, ['tanod', 'admin']))
                    <x-sidebar-link icon="moon" :href="route('curfew.create')" :active="request()->routeIs('curfew.create')">
                        {{ __('Log Curfew') }}
                    </x-sidebar-link>
                @endif

                @if (in_array($role, ['admin', 'official']))
                <p class="{{ $groupLabel }} pt-4">{{ __('Administration') }}</p>
                <x-sidebar-link icon="history" :href="route('admin.auditLogs')" :active="request()->routeIs('admin.auditLogs')">
                    {{ __('Audit Logs') }}
                </x-sidebar-link>
                @if (in_array($role, ['admin', 'official']))
                    <x-sidebar-link icon="shield" :href="route('admin.verifications.index')" :active="request()->routeIs('admin.verifications.*')">
                        {{ __('Residence Verification') }}
                    </x-sidebar-link>
                    <x-sidebar-link icon="user-plus" :href="route('admin.staff.create')" :active="request()->routeIs('admin.staff.create')">
                        {{ __('Add Staff') }}
                    </x-sidebar-link>
                @endif
                @endif

            @endif

        </nav>

        <!-- Bottom section -->
        <div class="border-t border-white/15 bg-black/20 p-3 space-y-2 shrink-0">
            @php
                $accountUser = Auth::user();
                $roleLabel = match ($accountUser->role) {
                    'official' => __('Barangay Official'),
                    'tanod'    => __('Barangay Tanod'),
                    'admin'    => __('Admin'),
                    'resident' => __('Resident'),
                    default    => ucfirst($accountUser->role),
                };
                $residentVerification = $accountUser->role === 'resident' ? $accountUser->residentVerification : null;
                $isResidenceVerified = $residentVerification?->status === \App\Models\ResidentVerification::VERIFIED;
            @endphp

            <!-- Profile (menu opens upward) -->
            <div x-data="{ menu: false }" @click.outside="menu = false" @keydown.escape.window="if (menu) { menu = false; $nextTick(() => $refs.accountTrigger.focus()); }" class="relative">
                <div x-show="menu" x-cloak
                     x-transition:enter="t-dropdown"
                     x-transition:enter-start="t-dropdown-start"
                     x-transition:enter-end="t-dropdown-open"
                     x-transition:leave="t-dropdown"
                     x-transition:leave-start="t-dropdown-open"
                     x-transition:leave-end="t-dropdown-close"
                     id="account-menu" aria-label="{{ __('Account menu') }}" role="group"
                     :class="collapsed ? 'lg:w-64' : ''"
                     class="absolute bottom-full left-0 right-0 mb-2 rounded-xl border border-stone-200 cg-surface-raised py-1 shadow-lg dark:border-gray-700 dark:bg-[#262624]">
                    <div class="px-4 pt-1 pb-1">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500 dark:text-gray-400">{{ __('Language') }}</span>
                    </div>
                    @foreach (['en' => __('English'), 'tl' => __('Tagalog')] as $code => $name)
                        <a href="{{ route('locale.switch', $code) }}" class="flex items-center justify-between px-4 py-3 lg:py-2 text-sm text-stone-700 hover:bg-stone-100 dark:text-gray-200 dark:hover:bg-white/5" @if (app()->getLocale() === $code) aria-current="true" @endif>
                            <span>{{ $name }}</span>
                            @if (app()->getLocale() === $code) <span aria-hidden="true">&#10003;</span> @endif
                        </a>
                    @endforeach
                    <div class="mx-4 my-1 border-t border-[#c9c1b6] dark:border-[#55534f]"></div>
                    <button type="button" role="switch" aria-label="{{ __('Toggle dark mode') }}" :aria-checked="darkMode.toString()" @click="document.documentElement.classList.add('cg-theme-switching'); darkMode = !darkMode; requestAnimationFrame(() => requestAnimationFrame(() => document.documentElement.classList.remove('cg-theme-switching')))"
                            class="flex min-h-11 w-full items-center justify-between px-4 py-2 text-left text-sm text-stone-700 transition-colors hover:bg-stone-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-inset focus-visible:outline-maroon-700 dark:text-gray-200 dark:hover:bg-white/5">
                        <span class="flex items-center gap-2">
                            <svg x-show="!darkMode" class="h-4 w-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="12" cy="12" r="4"></circle><path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l-1.42 1.42m11.3-11.3-1.42-1.42"></path>
                            </svg>
                            <svg x-show="darkMode" class="h-4 w-4 text-maroon-600 dark:text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.9 13A8.5 8.5 0 0 1 11 3.1 8.5 8.5 0 1 0 20.9 13Z"></path>
                            </svg>
                            <span>{{ __('Appearance') }}</span>
                        </span>
                        <span class="text-xs text-stone-600 dark:text-gray-400" x-text='darkMode ? @js(__('Dark')) : @js(__('Light'))'></span>
                    </button>
                    <div class="mx-4 my-1 border-t border-[#c9c1b6] dark:border-[#55534f]"></div>
                    <a href="{{ route('profile.edit') }}" class="flex min-h-11 items-center justify-between gap-3 px-4 py-2 text-sm text-stone-700 transition-colors hover:bg-stone-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-inset focus-visible:outline-maroon-700 dark:text-gray-200 dark:hover:bg-white/5">
                        <span class="flex min-w-0 items-center gap-2">
                            <svg class="h-4 w-4 shrink-0 text-stone-500 dark:text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <circle cx="12" cy="8" r="3.25" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 20a6.5 6.5 0 0 1 13 0" />
                            </svg>
                            <span>{{ __('Profile') }}</span>
                        </span>
                        @if ($accountUser->role === 'resident' && ! $isResidenceVerified)
                            @if ($residentVerification?->status === \App\Models\ResidentVerification::PENDING)
                                <span class="ml-auto inline-flex shrink-0 items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">{{ __('Pending review') }}</span>
                            @else
                                <span class="ml-auto inline-flex shrink-0 items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-900 dark:bg-amber-900/40 dark:text-amber-200">
                                    <span aria-hidden="true" class="inline-flex h-3.5 w-3.5 items-center justify-center rounded-full border border-current text-[9px] leading-none">!</span>
                                    {{ __('Verify residency!') }}
                                </span>
                            @endif
                        @endif
                    </a>
                    <div class="mx-4 my-1 border-t border-[#c9c1b6] dark:border-[#55534f]"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-3 lg:py-2 text-left text-sm font-semibold text-red-700 hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-inset focus-visible:outline-red-600 dark:text-red-400 dark:hover:bg-red-400/10 dark:focus-visible:outline-red-400">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>

                <button type="button" x-ref="accountTrigger" @click="menu = !menu" :aria-expanded="menu.toString()" aria-controls="account-menu" aria-label="{{ $accountUser->name }} {{ __('Account menu') }}" :title='collapsed ? @js($accountUser->name) : null' class="cg-sidebar-account w-full flex items-center gap-3 rounded-xl px-2 py-2 text-left hover:bg-white/10 transition">
                    @if ($accountUser->avatarPath())
                        <img src="{{ route('profile.photo', ['v' => basename($accountUser->avatarPath())]) }}" alt="" class="h-9 w-9 shrink-0 rounded-full object-cover ring-1 ring-white/30">
                    @else
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#fffaf5] text-sm font-semibold text-maroon-800">
                            {{ strtoupper(mb_substr($accountUser->name, 0, 1)) }}
                        </span>
                    @endif
                    <span class="cg-sidebar-label flex min-w-0 flex-1 flex-col gap-1 leading-tight">
                        <span class="block truncate text-sm font-semibold text-white">{{ $accountUser->name }}</span>
                        <span class="flex min-w-0 items-center justify-start gap-2">
                            <span class="truncate text-xs font-medium text-white/70">{{ $roleLabel }}</span>
                            @if ($isResidenceVerified)
                                <span class="inline-flex shrink-0 items-center gap-0.5 rounded-full bg-emerald-100 px-1.5 py-0.5 text-[9px] font-bold leading-none text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-200" title="{{ __('Verified resident') }}">
                                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.07 7.13a1 1 0 0 1-1.42.006L3.29 8.71a1 1 0 1 1 1.42-1.407l4.217 4.26 6.363-6.414a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd"/></svg>
                                    {{ __('Verified') }}
                                </span>
                            @endif
                        </span>
                    </span>
                    <svg class="cg-sidebar-label h-4 w-4 shrink-0 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                    </svg>
                </button>
            </div>
        </div>
    </aside>
</div>
