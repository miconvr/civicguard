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
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $verification->user->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $verification->user->email }}</p>
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

                    <div x-data="{ revealed: false }" class="group relative min-h-48 overflow-hidden rounded-xl border border-stone-200 bg-stone-100 dark:border-gray-700 dark:bg-black/20">
                        <div x-show="!revealed" class="absolute inset-0 flex items-center justify-center bg-[repeating-linear-gradient(135deg,#e7e5e4_0px,#e7e5e4_10px,#f5f5f4_10px,#f5f5f4_20px)] p-4 dark:bg-[repeating-linear-gradient(135deg,#292524_0px,#292524_10px,#1c1917_10px,#1c1917_20px)]">
                            <span class="pointer-events-none absolute text-xs font-medium text-stone-600 transition-opacity group-hover:opacity-0 dark:text-gray-300">{{ __('Submission hidden') }}</span>
                            <button type="button" @click="revealed = true" :aria-expanded="revealed" class="z-10 rounded-lg bg-stone-900/85 px-4 py-3 text-sm font-semibold text-white opacity-0 shadow-lg transition duration-200 group-hover:scale-105 group-hover:bg-maroon-800 group-hover:opacity-100 group-focus-within:opacity-100 [@media(hover:none)]:opacity-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-maroon-700 dark:bg-black/80">
                                {{ __('Show submission') }}
                            </button>
                        </div>
                        <template x-if="revealed">
                            <div class="flex min-h-48 flex-col items-center justify-center gap-2 p-2">
                                <a href="{{ route('admin.verifications.document', $verification) }}" target="_blank" rel="noopener" class="flex min-h-40 w-full items-center justify-center focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-maroon-700">
                                    <img src="{{ route('admin.verifications.document', $verification) }}" alt="{{ __('Residency proof submitted by :name', ['name' => $verification->user->name]) }}" class="max-h-72 w-full object-contain">
                                </a>
                                <button type="button" @click="revealed = false" class="text-xs font-semibold text-stone-600 underline underline-offset-2 hover:text-maroon-800 dark:text-gray-300 dark:hover:text-white">{{ __('Hide submission') }}</button>
                            </div>
                        </template>
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
