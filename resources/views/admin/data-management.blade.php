<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Data Management') }}</h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 dark:border-emerald-900 dark:bg-emerald-900/20 dark:text-emerald-200">{{ session('status') }}</div>
            @endif

            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Manage test data for the application. This page is only available to administrators.') }}</p>

            <section class="rounded-xl border border-red-200 bg-red-50/70 p-5 sm:p-6 dark:border-red-900/70 dark:bg-red-950/20" aria-labelledby="reset-data-title">
                <div class="max-w-3xl">
                    <h3 id="reset-data-title" class="text-lg font-semibold text-red-900 dark:text-red-200">{{ __('Reset test data') }}</h3>
                    <p class="mt-2 text-sm leading-6 text-red-800 dark:text-red-300">{{ __('This permanently deletes all incident reports, assignments, curfew logs, notifications, chatbot history, residence verification records, audit logs, report photos, and verification documents. User accounts, profile pictures, and report categories are kept.') }}</p>

                    <form method="POST" action="{{ route('admin.data.reset') }}" class="mt-5 grid gap-4 sm:grid-cols-2" onsubmit="return confirm('{{ __('This cannot be undone. Delete all test data now?') }}')">
                        @csrf
                        @method('DELETE')

                        <div>
                            <label for="reset-confirmation" class="mb-1 block text-sm font-medium text-red-900 dark:text-red-200">{{ __('Type DELETE ALL TEST DATA to confirm') }}</label>
                            <input id="reset-confirmation" name="confirmation" type="text" required autocomplete="off" class="cg-select w-full" placeholder="DELETE ALL TEST DATA">
                            @error('confirmation')
                                <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="reset-password" class="mb-1 block text-sm font-medium text-red-900 dark:text-red-200">{{ __('Confirm your password') }}</label>
                            <input id="reset-password" name="password" type="password" required autocomplete="current-password" class="cg-select w-full">
                            @error('password')
                                <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700">{{ __('Delete all test data') }}</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
