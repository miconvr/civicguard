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

                @if ($unread > 0)
                    <div class="flex items-center justify-between gap-3 border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $unread }} {{ __('unread') }}</p>
                        <form method="POST" action="{{ route('notifications.readAll') }}">
                            @csrf
                            <button class="min-h-[44px] px-2 text-sm font-medium text-maroon-700 dark:text-maroon-300 hover:underline">{{ __('Mark all as read') }}</button>
                        </form>
                    </div>
                @endif

                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($notifications as $notification)
                        <li>
                            <form method="POST" action="{{ route('notifications.open', $notification) }}">
                                @csrf
                                <button type="submit" class="flex w-full min-h-[56px] items-start gap-3 px-4 py-3 text-left transition hover:bg-black/5 dark:hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-maroon-600 {{ $notification->is_read ? '' : 'bg-maroon-50/60 dark:bg-maroon-900/20' }}">
                                    <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full {{ $notification->is_read ? 'bg-transparent' : 'bg-maroon-700 dark:bg-maroon-300' }}" aria-hidden="true"></span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-sm {{ $notification->is_read ? 'text-gray-700 dark:text-gray-300' : 'font-semibold text-gray-900 dark:text-gray-100' }}">
                                            @unless ($notification->is_read)<span class="sr-only">{{ __('Unread') }}: </span>@endunless
                                            {{ $notification->message }}
                                        </span>
                                        <span class="mt-0.5 block text-sm text-gray-600 dark:text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
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
