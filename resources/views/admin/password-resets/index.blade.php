<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Reset Resident Passwords') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div role="status" class="font-medium text-sm text-green-700 bg-green-50 dark:bg-green-900/30 dark:text-green-300 px-4 py-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <div class="cg-card p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Staff-assisted password reset') }}</h3>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600 dark:text-gray-300">
                    {{ __('Use this only after confirming the resident’s identity. Authorized staff should give the resident the new temporary password privately and ask them to change it after signing in.') }}
                </p>
            </div>

            <div class="cg-card overflow-hidden">
                <form method="GET" action="{{ route('admin.passwordResets.index') }}" class="flex flex-col gap-3 border-b border-gray-200 p-6 sm:flex-row sm:items-end dark:border-gray-700">
                    <div class="flex-1">
                        <label for="resident-search" class="cg-label">{{ __('Find a resident') }}</label>
                        <input id="resident-search" type="search" name="search" value="{{ $search }}" maxlength="100" placeholder="{{ __('Search by name or email…') }}" class="cg-input min-h-11 w-full" autocomplete="off">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="cg-btn min-h-11">{{ __('Search') }}</button>
                        @if ($search !== '')
                            <a href="{{ route('admin.passwordResets.index') }}" class="inline-flex min-h-11 items-center rounded-lg border border-gray-300 px-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800">
                                {{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </form>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <caption class="sr-only">{{ __('Registered residents and password reset forms') }}</caption>
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">{{ __('Resident') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">{{ __('New temporary password') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-transparent">
                            @forelse ($residents as $resident)
                                <tr>
                                    <th scope="row" class="px-6 py-4 text-left align-top">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $resident->name }}</div>
                                        <div class="mt-1 text-sm font-normal text-gray-600 dark:text-gray-300">{{ $resident->email }}</div>
                                    </th>
                                    <td class="px-6 py-4 align-top">
                                        <form id="reset-resident-{{ $resident->id }}" method="POST" action="{{ route('admin.passwordResets.update', $resident) }}" class="space-y-2" data-confirm="{{ __('Reset this resident’s password? Confirm their identity first.') }}">
                                            @csrf
                                            @method('PATCH')
                                            <label class="sr-only" for="password-{{ $resident->id }}">{{ __('New temporary password for :name', ['name' => $resident->name]) }}</label>
                                            <input id="password-{{ $resident->id }}" type="password" name="password" minlength="8" required autocomplete="new-password" class="cg-input min-w-56" placeholder="{{ __('At least 8 characters') }}">
                                            <label class="sr-only" for="password-confirmation-{{ $resident->id }}">{{ __('Confirm temporary password for :name', ['name' => $resident->name]) }}</label>
                                            <input id="password-confirmation-{{ $resident->id }}" type="password" name="password_confirmation" minlength="8" required autocomplete="new-password" class="cg-input min-w-56" placeholder="{{ __('Confirm password') }}">
                                            @error('password', 'reset-'.$resident->id)
                                                <p class="cg-error">{{ $message }}</p>
                                            @enderror
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <button type="submit" form="reset-resident-{{ $resident->id }}" class="cg-btn whitespace-nowrap">
                                            {{ __('Reset password') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-600 dark:text-gray-300">{{ __('No residents found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($residents->hasPages())
                    <div class="border-t border-gray-200 px-6 py-3 dark:border-gray-700">{{ $residents->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
