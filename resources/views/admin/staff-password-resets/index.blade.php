<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Reset Staff Passwords') }}
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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Staff password reset') }}</h3>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600 dark:text-gray-300">
                    {{ __('Use this only after confirming the staff member in person. Give the temporary password privately. They must choose a new password the next time they sign in.') }}
                </p>
                <p class="mt-3 text-sm">
                    <a href="{{ route('admin.passwordResets.index') }}" class="font-semibold text-maroon-700 hover:underline dark:text-gray-200">{{ __('Reset a resident password') }}</a>
                </p>
            </div>

            <div class="cg-card overflow-hidden">
                <form method="GET" action="{{ route('admin.staffPasswordResets.index') }}" class="flex flex-col gap-3 border-b border-gray-200 p-6 sm:flex-row sm:items-end dark:border-gray-700">
                    <div class="flex-1">
                        <label for="staff-search" class="cg-label">{{ __('Find a staff member') }}</label>
                        <input id="staff-search" type="search" name="search" value="{{ $search }}" maxlength="100" placeholder="{{ __('Search by name or email') }}" class="cg-input min-h-11 w-full">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="cg-btn min-h-11">{{ __('Search') }}</button>
                        @if ($search !== '')
                            <a href="{{ route('admin.staffPasswordResets.index') }}" class="inline-flex min-h-11 items-center rounded-lg border border-gray-300 px-4 text-sm font-semibold text-gray-700 dark:border-gray-600 dark:text-gray-200">{{ __('Clear') }}</a>
                        @endif
                    </div>
                </form>

                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($staffMembers as $member)
                        <form method="POST" action="{{ route('admin.staffPasswordResets.update', $member) }}" class="grid gap-4 p-6 sm:grid-cols-[1fr_1fr_auto] sm:items-start">
                            @csrf
                            @method('PATCH')
                            <div class="min-w-0">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $member->name }}</div>
                                <div class="mt-1 break-all text-sm text-gray-600 dark:text-gray-300">{{ $member->email }}</div>
                                <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">{{ __(ucfirst($member->role)) }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="sr-only" for="password-{{ $member->id }}">{{ __('New temporary password for :name', ['name' => $member->name]) }}</label>
                                <input id="password-{{ $member->id }}" type="password" name="password" minlength="8" required autocomplete="new-password" placeholder="{{ __('New temporary password') }}" class="cg-input min-h-11 w-full">
                                <label class="sr-only" for="password-confirmation-{{ $member->id }}">{{ __('Confirm temporary password for :name', ['name' => $member->name]) }}</label>
                                <input id="password-confirmation-{{ $member->id }}" type="password" name="password_confirmation" minlength="8" required autocomplete="new-password" placeholder="{{ __('Confirm password') }}" class="cg-input min-h-11 w-full">
                                @error('password', 'reset-'.$member->id)
                                    <p class="cg-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="cg-btn min-h-11 w-full whitespace-nowrap sm:w-auto">{{ __('Reset password') }}</button>
                        </form>
                    @empty
                        <p class="px-6 py-10 text-center text-sm text-gray-600 dark:text-gray-300">{{ __('No staff found.') }}</p>
                    @endforelse
                </div>

                @if ($staffMembers->hasPages())
                    <div class="border-t border-gray-200 px-6 py-3 dark:border-gray-700">{{ $staffMembers->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
