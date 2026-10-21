<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Residence Verification') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 dark:border-emerald-900 dark:bg-emerald-900/20 dark:text-emerald-200">{{ session('status') }}</div>
            @endif

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Review submitted proof images. Verified residents receive a permanent badge. Submitted images are deleted after a decision.') }}</p>
            </div>

            <form method="get" action="{{ route('admin.verifications.index') }}" class="cg-card flex flex-col gap-3 p-4 sm:flex-row">
                <label for="verification-search" class="sr-only">{{ __('Search applicants') }}</label>
                <input id="verification-search" type="search" name="search" value="{{ $search }}" maxlength="100" placeholder="{{ __('Search by name or email…') }}" class="min-h-11 flex-1 rounded-lg border-stone-300 bg-white text-sm shadow-sm focus:border-maroon-700 focus:ring-maroon-700 dark:border-gray-700 dark:bg-[#1c1c1a] dark:text-gray-100">
                <div class="flex gap-2">
                    <x-primary-button>{{ __('Search') }}</x-primary-button>
                    @if ($search !== '')
                        <a href="{{ route('admin.verifications.index') }}" class="inline-flex min-h-11 items-center rounded-lg border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-stone-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-white/5">{{ __('Clear') }}</a>
                    @endif
                </div>
            </form>

            @forelse ($verifications as $verification)
                <article class="cg-card grid gap-5 p-4 sm:p-6 md:grid-cols-[minmax(0,1fr)_16rem]">
                    <div>
                        @php($residentAvatar = $verification->user->avatarPath())
                        <div class="flex items-center gap-3">
                            @if ($residentAvatar)
                                <img src="{{ route('admin.verifications.avatar', $verification) }}" alt="{{ __('Profile picture of :name', ['name' => $verification->user->name]) }}" class="h-11 w-11 shrink-0 rounded-full object-cover ring-1 ring-stone-200 dark:ring-gray-700">
                            @else
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-maroon-100 text-sm font-semibold text-maroon-800 dark:bg-white/10 dark:text-gray-100" aria-hidden="true">{{ strtoupper(mb_substr($verification->user->name, 0, 1)) }}</span>
                            @endif
                            <div class="min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $verification->user->name }}</h3>
                                <p class="break-all text-sm text-gray-600 dark:text-gray-400">{{ $verification->user->email }}</p>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">{{ __('Submitted') }} {{ $verification->submitted_at->diffForHumans() }}</p>

                        <div class="mt-5 flex flex-wrap gap-3">
                            <form method="post" action="{{ route('admin.verifications.decide', $verification) }}">
                                @csrf
                                @method('patch')
                                <input type="hidden" name="decision" value="verified">
                                <x-primary-button>{{ __('Verify resident') }}</x-primary-button>
                            </form>
                            <form method="post" action="{{ route('admin.verifications.decide', $verification) }}" onsubmit="return confirm('{{ __('Decline this verification request?') }}')">
                                @csrf
                                @method('patch')
                                <input type="hidden" name="decision" value="rejected">
                                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-red-300 px-4 py-2 text-sm font-semibold text-red-800 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-red-900 dark:text-red-300 dark:hover:bg-red-900/20">{{ __('Decline') }}</button>
                            </form>
                        </div>
                    </div>

                    <div x-data="{ revealed: false, viewerOpen: false }" @keydown.escape.window="viewerOpen = false" class="group relative min-h-48 overflow-hidden rounded-xl border border-stone-200 bg-stone-100 dark:border-gray-700 dark:bg-black/20">
                        <div x-show="!revealed" class="absolute inset-0 flex items-center justify-center bg-[repeating-linear-gradient(135deg,#e7e5e4_0px,#e7e5e4_10px,#f5f5f4_10px,#f5f5f4_20px)] p-4 dark:bg-[repeating-linear-gradient(135deg,#292524_0px,#292524_10px,#1c1917_10px,#1c1917_20px)]">
                            <span class="pointer-events-none absolute text-xs font-medium text-stone-600 transition-opacity group-hover:opacity-0 dark:text-gray-300">{{ __('Submission hidden') }}</span>
                            <button type="button" @click="revealed = true" :aria-expanded="revealed" class="z-10 rounded-lg bg-stone-900/85 px-4 py-3 text-sm font-semibold text-white opacity-0 shadow-lg transition duration-200 group-hover:scale-105 group-hover:bg-maroon-800 group-hover:opacity-100 group-focus-within:opacity-100 [@media(hover:none)]:opacity-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-maroon-700 dark:bg-black/80">
                                {{ __('Show submission') }}
                            </button>
                        </div>
                        <template x-if="revealed">
                            <div class="flex min-h-48 flex-col items-center justify-center gap-2 p-2">
                                <button type="button" @click="viewerOpen = true" aria-label="{{ __('View larger image') }}" class="group/image relative flex min-h-40 w-full items-center justify-center focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-maroon-700">
                                    <img src="{{ route('admin.verifications.document', $verification) }}" alt="{{ __('Residency proof submitted by :name', ['name' => $verification->user->name]) }}" class="max-h-72 w-full object-contain">
                                    <span class="absolute right-2 top-2 inline-flex h-8 w-8 items-center justify-center rounded-lg border border-stone-200 bg-white/95 text-stone-500 shadow-sm transition group-hover/image:text-stone-800 dark:border-gray-600 dark:bg-gray-900/90 dark:text-gray-300" aria-hidden="true">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3h5v5M17 3l-6 6M8 17H3v-5m0 5 6-6"/></svg>
                                    </span>
                                </button>
                                <button type="button" @click="revealed = false" class="text-xs font-semibold text-stone-600 underline underline-offset-2 hover:text-maroon-800 dark:text-gray-300 dark:hover:text-white">{{ __('Hide submission') }}</button>
                            </div>
                        </template>

                        <div x-show="viewerOpen" x-cloak x-transition.opacity @click.self="viewerOpen = false" role="dialog" aria-modal="true" aria-label="{{ __('Residency proof submitted by :name', ['name' => $verification->user->name]) }}" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 p-4 sm:p-8">
                            <div class="relative flex max-h-full max-w-full items-center justify-center">
                                <button type="button" @click="viewerOpen = false" aria-label="{{ __('Close larger image') }}" class="absolute -right-2 -top-2 z-10 inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 bg-stone-700 text-white shadow-lg transition hover:bg-stone-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white sm:right-2 sm:top-2">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="m5 5 10 10M15 5 5 15"/></svg>
                                </button>
                                <template x-if="viewerOpen">
                                    <img src="{{ route('admin.verifications.document', $verification) }}" alt="{{ __('Residency proof submitted by :name', ['name' => $verification->user->name]) }}" class="max-h-[88vh] max-w-[92vw] rounded-lg bg-white object-contain shadow-2xl">
                                </template>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="cg-card p-8 text-center">
                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $search !== '' ? __('No matching verification requests.') : __('No verification requests are waiting.') }}</p>
                </div>
            @endforelse

            @if ($verifications->hasPages())
                <div>{{ $verifications->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
