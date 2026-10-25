<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Residence Verification') }}</h2>
    </x-slot>

    <div class="py-8">
        <div x-data="verificationTabs({ status: @js($status), description: @js($descriptions[$status]) })" x-on:click="navigate($event)" class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div role="status" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-900 dark:border-green-900 dark:bg-green-900/20 dark:text-green-200">{{ session('status') }}</div>
            @endif

            <p x-text="description" class="text-sm text-gray-600 dark:text-gray-400">{{ $descriptions[$status] }}</p>

            <nav aria-label="{{ __('Verification status') }}" class="flex flex-wrap gap-2">
                @foreach (['pending' => __('Pending'), 'verified' => __('Verified'), 'rejected' => __('Declined')] as $tabStatus => $tabLabel)
                    <a data-verification-tab href="{{ route('admin.verifications.index', ['status' => $tabStatus, 'search' => $search !== '' ? $search : null]) }}" @if ($status === $tabStatus) aria-current="page" @endif x-bind:aria-current="status === '{{ $tabStatus }}' ? 'page' : null" class="cg-verification-tab inline-flex min-h-11 items-center gap-2 rounded-lg border px-3 py-2 text-sm font-semibold transition duration-[var(--cg-motion-interaction)] ease-in-out hover:-translate-y-px hover:shadow-sm motion-reduce:transform-none motion-reduce:transition-none focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-maroon-700">
                        <span>{{ $tabLabel }}</span>
                        <span class="cg-verification-tab-count rounded-full px-2 py-0.5 text-xs">{{ $counts->get($tabStatus, 0) }}</span>
                    </a>
                @endforeach
                <span x-show="loading" x-cloak role="status" class="inline-flex min-h-10 items-center gap-2 px-2 text-sm text-gray-500 dark:text-gray-400">
                    <svg class="h-4 w-4 animate-spin motion-reduce:animate-none" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3"/><path class="opacity-80" fill="currentColor" d="M12 3a9 9 0 0 0-9 9h3a6 6 0 0 1 6-6V3Z"/></svg>
                    {{ __('Loading verification records…') }}
                </span>
            </nav>

            <form method="get" action="{{ route('admin.verifications.index') }}" x-data="{ searching: false }" @submit="searching = true" class="cg-card flex flex-col gap-3 p-4 sm:flex-row">
                <input type="hidden" name="status" value="{{ $status }}" x-bind:value="status">
                <label for="verification-search" class="sr-only">{{ __('Search applicants') }}</label>
                <input id="verification-search" type="search" name="search" value="{{ $search }}" maxlength="100" placeholder="{{ __('Search by name or email') }}" class="cg-input min-h-11 flex-1 text-sm shadow-sm">
                <div class="flex gap-2">
                    <x-primary-button x-bind:disabled="searching" class="gap-2 disabled:cursor-wait disabled:opacity-80">
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

            <div id="verification-results" aria-live="polite" x-bind:aria-busy="loading" class="space-y-4">
                @include('admin.verifications.partials.results')
            </div>
        </div>
    </div>
</x-app-layout>
