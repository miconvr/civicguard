@props([
    'mobile' => false,
])

@php
    $user = auth()->user();
    $notifications = $user
        ? \App\Models\AppNotification::where('user_id', $user->id)->with('report')->latest()->take(8)->get()
        : collect();
    $unreadCount = $user
        ? \App\Models\AppNotification::where('user_id', $user->id)->where('is_read', false)->count()
        : 0;
@endphp

<div x-data="{
    open: false,
    unread: {{ $unreadCount }},
    toggle() {
        this.open = !this.open;
        if (this.open && this.unread > 0) {
            this.unread = 0;
            fetch('{{ route('notifications.readAll') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).catch(() => {});
        }
    }
}"
@click.outside="open = false"
@keydown.escape.window="open = false"
class="relative inline-flex items-center">

    <button type="button"
            @click="toggle()"
            :aria-expanded="open.toString()"
            aria-haspopup="true"
            title="{{ __('Notifications') }}"
            class="cg-notification-trigger relative inline-flex h-10 w-10 items-center justify-center rounded-xl transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-maroon-700 {{ $mobile ? 'text-stone-700 hover:bg-black/5 dark:text-gray-200 dark:hover:bg-white/5 focus-visible:outline-offset-0' : 'border border-stone-200 bg-white text-stone-700 shadow-sm hover:border-maroon-700 hover:text-maroon-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-200 dark:hover:bg-white/10 dark:hover:text-white' }} {{ request()->routeIs('notifications.index') ? ($mobile ? '!text-maroon-700 dark:!text-white' : '!border-maroon-700 !text-maroon-700 dark:!border-maroon-500 dark:!text-white') : '' }}">
        <span class="sr-only">{{ __('Notifications') }}</span>
        <svg class="{{ $mobile ? 'h-6 w-6' : 'h-5 w-5' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        <span x-show="unread > 0"
              x-cloak
              x-transition.opacity
              class="absolute {{ $mobile ? 'top-1.5 right-1.5 h-4 min-w-[1rem] text-[10px]' : '-top-1 -right-1 h-5 min-w-[1.25rem] text-[11px]' }} flex items-center justify-center rounded-full bg-maroon-700 px-1 font-bold text-white shadow-sm ring-2 ring-white dark:bg-maroon-600 dark:ring-gray-900">
            <span x-text="unread > 99 ? '99+' : unread"></span>
            <span class="sr-only"> {{ __('unread') }}</span>
        </span>
    </button>

    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-[var(--cg-ease-out)] duration-[var(--cg-motion-enter)]"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-[var(--cg-ease-in)] duration-[var(--cg-motion-exit)]"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 top-full mt-2 w-80 sm:w-96 max-w-[calc(100vw-1.5rem)] rounded-2xl border border-stone-200 cg-surface shadow-2xl dark:border-white/10 z-50 overflow-hidden"
         style="display: none;">

        <div class="flex items-center justify-between border-b border-stone-100 px-4 py-3 dark:border-white/10 bg-stone-50/50 dark:bg-white/[0.02]">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-bold text-stone-900 dark:text-stone-100">{{ __('Notifications') }}</h3>
            </div>
            <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-maroon-700 hover:text-maroon-800 dark:text-[#c27872] dark:hover:text-white">
                {{ __('View all') }}
            </a>
        </div>

        <ul class="max-h-80 overflow-y-auto divide-y divide-stone-100 dark:divide-white/5 overscroll-contain">
            @forelse ($notifications as $notification)
                <li>
                    <form method="POST" action="{{ route('notifications.open', $notification) }}">
                        @csrf
                        <button type="submit" class="flex w-full min-h-[52px] items-start gap-3 p-3.5 text-left transition hover:bg-stone-50 dark:hover:bg-white/5 focus:outline-none focus:bg-stone-50 dark:focus:bg-white/5 {{ $notification->is_read ? '' : 'bg-maroon-50/40 dark:bg-maroon-900/10' }}">
                            @if (is_null($notification->report_id))
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300" aria-label="{{ __('Profile update') }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                                    </svg>
                                </span>
                            @else
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $notification->is_read ? 'bg-transparent' : 'bg-maroon-700 dark:bg-[#c27872]' }}" aria-hidden="true"></span>
                            @endif
                            <span class="min-w-0 flex-1">
                                <span class="block text-xs sm:text-sm leading-snug {{ $notification->is_read ? 'text-stone-700 dark:text-stone-300' : 'font-semibold text-stone-900 dark:text-stone-100' }}">
                                    {{ $notification->message }}
                                </span>
                                <span class="mt-1 block text-[11px] text-stone-600 dark:text-stone-400">
                                    {{ \App\Support\LocalizedDate::relative($notification->created_at) }}
                                </span>
                            </span>
                            <span class="mt-1 text-stone-400 text-xs" aria-hidden="true">&rsaquo;</span>
                        </button>
                    </form>
                </li>
            @empty
                <li class="px-4 py-8 text-center">
                    <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-stone-100 text-stone-400 dark:bg-white/5 dark:text-stone-500">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-stone-800 dark:text-stone-200">{{ __("You're all caught up.") }}</p>
                    <p class="mt-0.5 text-[11px] text-stone-600 dark:text-stone-400">{{ __('No notifications right now.') }}</p>
                </li>
            @endforelse
        </ul>

        @if ($notifications->isNotEmpty())
            <div class="border-t border-stone-100 bg-stone-50/50 p-2 text-center dark:border-white/10 dark:bg-white/[0.02]">
                <a href="{{ route('notifications.index') }}" class="block w-full py-1.5 text-center text-xs font-semibold text-stone-600 hover:text-maroon-700 dark:text-stone-400 dark:hover:text-white transition">
                    {{ __('See all notification history') }} &rarr;
                </a>
            </div>
        @endif

    </div>
</div>

