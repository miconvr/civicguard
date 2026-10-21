<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Residence Verification') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div role="status" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-900 dark:border-green-900 dark:bg-green-900/20 dark:text-green-200">{{ session('status') }}</div>
            @endif

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    @if ($status === 'pending')
                        {{ __('Review submitted proof images. Verified residents receive a permanent badge. Submitted images are deleted after a decision.') }}
                    @elseif ($status === 'verified')
                        {{ __('Previously verified residents are listed here. Their proof images were deleted after review.') }}
                    @else
                        {{ __('Declined requests are kept here for review history. Their proof images were deleted after the decision.') }}
                    @endif
                </p>
            </div>

            <nav aria-label="{{ __('Verification status') }}" class="flex flex-wrap gap-2">
                @foreach (['pending' => __('Pending'), 'verified' => __('Verified'), 'rejected' => __('Declined')] as $tabStatus => $tabLabel)
                    <a href="{{ route('admin.verifications.index', ['status' => $tabStatus, 'search' => $search !== '' ? $search : null]) }}" @if ($status === $tabStatus) aria-current="page" @endif class="inline-flex min-h-10 items-center gap-2 rounded-lg border px-3 py-2 text-sm font-semibold transition duration-150 ease-in-out hover:-translate-y-0.5 hover:shadow-sm motion-reduce:transform-none motion-reduce:transition-none focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-maroon-700 {{ $status === $tabStatus ? 'border-maroon-800 bg-maroon-800 text-white dark:border-[#8f1d39] dark:bg-[#8f1d39]' : 'border-stone-300 bg-white text-stone-700 hover:bg-stone-50 dark:border-gray-700 dark:bg-[#1c1c1a] dark:text-gray-200 dark:hover:bg-white/5' }}">
                        <span>{{ $tabLabel }}</span>
                        <span class="rounded-full px-2 py-0.5 text-xs {{ $status === $tabStatus ? 'bg-white/15 text-white' : 'bg-stone-100 text-stone-600 dark:bg-white/10 dark:text-gray-300' }}">{{ $counts->get($tabStatus, 0) }}</span>
                    </a>
                @endforeach
            </nav>

            <form method="get" action="{{ route('admin.verifications.index') }}" x-data="{ searching: false }" @submit="searching = true" class="cg-card flex flex-col gap-3 p-4 sm:flex-row">
                <input type="hidden" name="status" value="{{ $status }}">
                <label for="verification-search" class="sr-only">{{ __('Search applicants') }}</label>
                <input id="verification-search" type="search" name="search" value="{{ $search }}" maxlength="100" placeholder="{{ __('Search by name or email…') }}" class="min-h-11 flex-1 rounded-lg border-stone-300 bg-white text-sm shadow-sm focus:border-stone-400 focus:outline-none focus:ring-0 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-stone-500 dark:border-gray-700 dark:bg-[#1c1c1a] dark:text-gray-100 dark:focus:border-gray-500 dark:focus-visible:outline-gray-400">
                <div class="flex gap-2">
                    <x-primary-button x-bind:disabled="searching" class="gap-2 transition-transform duration-150 active:scale-[.97] focus:ring-0 focus:ring-offset-0 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-stone-500 dark:focus-visible:outline-gray-400 disabled:cursor-wait disabled:opacity-80">
                        <span x-show="!searching">{{ __('Search') }}</span>
                        <span x-show="searching" x-cloak class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin motion-reduce:animate-none" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3"/><path class="opacity-80" fill="currentColor" d="M12 3a9 9 0 0 0-9 9h3a6 6 0 0 1 6-6V3Z"/></svg>
                            {{ __('Searching...') }}
                        </span>
                    </x-primary-button>
                    @if ($search !== '')
                        <a href="{{ route('admin.verifications.index', ['status' => $status]) }}" class="inline-flex min-h-11 items-center rounded-lg border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-stone-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-white/5">{{ __('Clear') }}</a>
                    @endif
                </div>
            </form>

            @forelse ($verifications as $verification)
                <article style="--cg-verification-delay: {{ min($loop->index * 45, 225) }}ms" class="cg-card cg-verification-result-enter grid gap-5 p-4 sm:p-6 md:grid-cols-[minmax(0,1fr)_16rem]">
                    <div>
                        @php($residentAvatar = $verification->user->avatarPath())
                        <div class="flex items-center gap-3">
                            @if ($residentAvatar)
                                <img src="{{ route('admin.verifications.avatar', $verification) }}" alt="{{ __('Profile picture of :name', ['name' => $verification->user->name]) }}" class="h-11 w-11 shrink-0 rounded-full object-cover ring-1 ring-stone-200 dark:ring-gray-700">
                            @else
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#fffaf5] text-sm font-semibold text-maroon-800 ring-1 ring-stone-200" aria-hidden="true">{{ strtoupper(mb_substr($verification->user->name, 0, 1)) }}</span>
                            @endif
                            <div class="min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $verification->user->name }}</h3>
                                <p class="break-all text-sm text-gray-600 dark:text-gray-400">{{ $verification->user->email }}</p>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">{{ __('Submitted') }} {{ $verification->submitted_at->diffForHumans() }}</p>

                        @if ($status === 'pending')
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
                        @endif
                    </div>

                    @if ($status === 'pending')
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

                        <template x-teleport="body">
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
                        </template>
                    </div>
                    @else
                        <div class="flex min-h-32 flex-col justify-center rounded-xl border border-stone-200 bg-stone-50 p-4 dark:border-gray-700 dark:bg-white/5">
                            <span class="inline-flex w-fit items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold {{ $status === 'verified' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                @if ($status === 'verified')
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.07 7.13a1 1 0 0 1-1.42.006L3.29 8.71a1 1 0 1 1 1.42-1.407l4.217 4.26 6.363-6.414a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="m5 5 10 10M15 5 5 15"/></svg>
                                @endif
                                {{ $status === 'verified' ? __('Verified') : __('Declined') }}
                            </span>
                            <p class="mt-3 text-xs text-gray-600 dark:text-gray-400">{{ __('Reviewed') }} {{ $verification->reviewed_at?->diffForHumans() ?? '—' }}</p>
                            @if ($verification->reviewer)
                                <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ __('Reviewed by :name', ['name' => $verification->reviewer->name]) }}</p>
                            @endif
                        </div>
                    @endif
                </article>
            @empty
                <div class="cg-card p-8 text-center">
                    <p class="font-semibold text-gray-800 dark:text-gray-200">
                        @if ($search !== '')
                            {{ __('No matching verification requests.') }}
                        @elseif ($status === 'pending')
                            {{ __('No verification requests are waiting.') }}
                        @elseif ($status === 'verified')
                            {{ __('No verified residents yet.') }}
                        @else
                            {{ __('No declined requests.') }}
                        @endif
                    </p>
                </div>
            @endforelse

            @if ($verifications->hasPages())
                <div>{{ $verifications->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
