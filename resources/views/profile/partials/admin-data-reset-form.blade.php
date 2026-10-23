<section aria-labelledby="reset-data-title">
    @if (session('data-reset-status'))
        <div role="status" class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 dark:border-emerald-900 dark:bg-emerald-900/20 dark:text-emerald-200">{{ session('data-reset-status') }}</div>
    @endif

    <header>
        <h2 id="reset-data-title" class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Reset test data') }}</h2>
        <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">{{ __('This permanently deletes all incident reports, assignments, curfew logs, notifications, chatbot history, residence verification records, audit logs, report photos, and verification documents. User accounts, profile pictures, and report categories are kept.') }}</p>
    </header>

    <form method="POST" action="{{ route('admin.data.reset') }}" class="mt-5 grid gap-4 sm:grid-cols-2" data-confirm="{{ __('This cannot be undone. Delete all test data now?') }}">
        @csrf
        @method('DELETE')

        <div>
            <label for="reset-confirmation" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Type DELETE ALL TEST DATA to confirm') }}</label>
            <input id="reset-confirmation" name="confirmation" type="text" required autocomplete="off" class="cg-select w-full" placeholder="DELETE ALL TEST DATA">
            @error('confirmation')
                <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="reset-password" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Confirm your password') }}</label>
            <input id="reset-password" name="password" type="password" required autocomplete="current-password" class="cg-select w-full">
            @error('password')
                <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
            @enderror
        </div>

        <div class="sm:col-span-2">
            <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700">{{ __('Delete all test data') }}</button>
        </div>
    </form>
</section>
