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

                    <a href="{{ route('admin.verifications.document', $verification) }}" target="_blank" rel="noopener" class="flex min-h-48 items-center justify-center overflow-hidden rounded-xl border border-stone-200 bg-stone-50 p-2 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-maroon-700 dark:border-gray-700 dark:bg-black/20">
                        <img src="{{ route('admin.verifications.document', $verification) }}" alt="{{ __('Residency proof submitted by :name', ['name' => $verification->user->name]) }}" class="max-h-72 w-full object-contain">
                    </a>
                </article>
            @empty
                <div class="cg-card p-8 text-center">
                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ __('No verification requests are waiting.') }}</p>
                </div>
            @endforelse

            @if ($verifications->hasPages())
                <div>{{ $verifications->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
