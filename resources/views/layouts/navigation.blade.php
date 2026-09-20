<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 border-t-4 border-t-maroon-700 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-9 w-auto text-maroon-700" />
                        <span class="hidden lg:block leading-tight">
                            <span class="block font-bold text-sm text-maroon-800 dark:text-maroon-800">CivicGuard</span>                            <span class="block text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Brgy. Maimpis</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    @if (Auth::user()->role === 'resident')
                        <x-nav-link :href="route('reports.create')" :active="request()->routeIs('reports.create')">
                            {{ __('Report Incident') }}
                        </x-nav-link>
                        <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.index')">
                            {{ __('My Reports') }}
                        </x-nav-link>
                    @endif

                    @if (in_array(Auth::user()->role, ['tanod', 'admin', 'official']))
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('All Reports') }}
                        </x-nav-link>
                    @endif

                    @if (in_array(Auth::user()->role, ['tanod', 'admin', 'official']))
                        <x-nav-link :href="route('admin.analytics')" :active="request()->routeIs('admin.analytics')">
                            {{ __('Analytics') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.auditLogs')" :active="request()->routeIs('admin.auditLogs')">
                            {{ __('Audit Logs') }}
                        </x-nav-link>
                    @endif

                    @if (Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.staff.create')" :active="request()->routeIs('admin.staff.create')">
                            {{ __('Add Staff') }}
                        </x-nav-link>
                    @endif

                    @if (in_array(Auth::user()->role, ['tanod', 'admin']))
                        <x-nav-link :href="route('curfew.create')" :active="request()->routeIs('curfew.create')">
                            {{ __('Log Curfew') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Right Side: AI Assistant, Notifications, Dark Mode, Profile -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                
                @if (in_array(Auth::user()->role, ['resident', 'admin', 'official']))
                    <!-- AI Assistant Link (No icon, just styled text) -->
                    <a href="{{ route('chatbot.widget') }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-maroon-700 dark:hover:text-maroon-800 transition">
                        <span class="font-bold text-maroon-700 dark:text-maroon-800">AI</span> Assistant
                    </a>
                @endif

                <!-- Notifications Bell Icon -->
                <a href="{{ route('notifications.index') }}" class="relative text-gray-500 dark:text-gray-400 hover:text-maroon-700 dark:hover:text-maroon-800 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @php $unreadCount = \App\Models\AppNotification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                    @if ($unreadCount > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center">{{ $unreadCount }}</span>
                    @endif
                </a>

                <!-- Dark Mode Toggle -->
                <button @click="darkMode = !darkMode" class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 transition">
                    <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if (in_array(Auth::user()->role, ['resident', 'admin', 'official']))
                <x-responsive-nav-link :href="route('reports.create')" :active="request()->routeIs('reports.create')">
                    {{ __('Report Incident') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.index')">
                    {{ __('My Reports') }}
                </x-responsive-nav-link>
            @endif

            @if (in_array(Auth::user()->role, ['tanod', 'admin', 'official']))
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('All Reports') }}
                </x-responsive-nav-link>
            @endif

            @if (in_array(Auth::user()->role, ['tanod', 'admin', 'official']))
                <x-responsive-nav-link :href="route('admin.analytics')" :active="request()->routeIs('admin.analytics')">
                    {{ __('Analytics') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.auditLogs')" :active="request()->routeIs('admin.auditLogs')">
                    {{ __('Audit Logs') }}
                </x-responsive-nav-link>
            @endif

            @if (Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.staff.create')" :active="request()->routeIs('admin.staff.create')">
                    {{ __('Add Staff') }}
                </x-responsive-nav-link>
            @endif

            @if (in_array(Auth::user()->role, ['tanod', 'admin']))
                <x-responsive-nav-link :href="route('curfew.create')" :active="request()->routeIs('curfew.create')">
                    {{ __('Log Curfew') }}
                </x-responsive-nav-link>
            @endif

            @if (Auth::user()->role === 'resident')
                <!-- AI Assistant Mobile -->
                <x-responsive-nav-link :href="route('chatbot.widget')" :active="request()->routeIs('chatbot.widget')">
                    <span class="font-bold text-maroon-700 dark:text-maroon-800">AI</span> Assistant
                </x-responsive-nav-link>
            @endif

            <!-- Notifications Mobile -->
            <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.index')">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    {{ __('Notifications') }}
                    @php $unreadCount = \App\Models\AppNotification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                    @if ($unreadCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $unreadCount }}</span>
                    @endif
                </div>
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>