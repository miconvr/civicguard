<x-app-layout>
    <x-slot name="headerWidth">max-w-2xl</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card p-0 overflow-hidden">
                @if (session('status'))
                    <p role="status" class="border-b border-gray-200 px-4 py-3 text-sm text-green-800 dark:border-gray-700 dark:text-green-300">{{ session('status') }}</p>
                @endif

                @if ($notifications->total() > 0)
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $unread }} {{ __('unread') }}</p>
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($unread > 0)
                                <form method="POST" action="{{ route('notifications.readAll') }}">
                                    @csrf
                                    <button class="min-h-10 rounded-lg px-3 text-sm font-semibold text-maroon-700 hover:bg-maroon-50 dark:text-[#e3aaa4] dark:hover:bg-white/5">{{ __('Mark all as read') }}</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('notifications.clear') }}" onsubmit="return confirm(@js(__('Clear all notifications?')))">
                                @csrf
                                @method('delete')
                                <button class="min-h-10 rounded-lg border border-stone-300 px-3 text-sm font-semibold text-stone-700 hover:bg-stone-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-white/10">{{ __('Clear all') }}</button>
                            </form>
                        </div>
                    </div>
                @endif

                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($notifications as $notification)
                        <li class="{{ $notification->is_read ? '' : 'bg-maroon-50/40 dark:bg-white/[0.03]' }}">
                            <form method="POST" action="{{ route('notifications.open', $notification) }}" class="min-w-0 flex-1">
                                @csrf
                                <button type="submit" class="flex w-full min-h-[56px] items-start gap-3 px-4 py-3 text-left transition hover:bg-black/5 dark:hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-maroon-600">
                                    @if (is_null($notification->report_id))
                                        <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300" aria-label="{{ __('Profile update') }}">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                                            </svg>
                                        </span>
                                    @else
                                        <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full {{ $notification->is_read ? 'bg-transparent' : 'bg-maroon-700 dark:bg-[#c27872]' }}" aria-hidden="true"></span>
                                    @endif
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-sm {{ $notification->is_read ? 'text-gray-700 dark:text-gray-300' : 'font-semibold text-gray-900 dark:text-gray-100' }}">
                                            @unless ($notification->is_read)<span class="sr-only">{{ __('Unread') }}: </span>@endunless
                                            {{ $notification->message }}
                                        </span>
                                        <span class="mt-0.5 block text-sm text-gray-600 dark:text-gray-400">{{ \App\Support\LocalizedDate::relative($notification->created_at) }}</span>
                                    </span>
                                    <span class="mt-1 text-gray-400" aria-hidden="true">&rsaquo;</span>
                                </button>
                            </form>
                        </li>
                    @empty
                        <li class="px-4 py-12 text-center">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ __("You're all caught up.") }}</p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('No notifications yet.') }}</p>
                        </li>
                    @endforelse
                </ul>

                @if ($notifications->hasPages())
                    <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3">{{ $notifications->links() }}</div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
