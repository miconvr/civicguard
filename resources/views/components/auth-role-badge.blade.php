@props(['role' => 'resident'])

@if ($role === 'staff')
    <div class="-mx-6 mb-6 flex items-center gap-3 bg-maroon-800 px-6 py-3 text-white sm:-mx-8 sm:px-8">
        <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 3v6c0 4.5-3.2 8.2-8 9-4.8-.8-8-4.5-8-9V6l8-3z"/></svg>
        <div class="leading-tight">
            <p class="text-sm font-bold uppercase tracking-wide">{{ __('Staff only') }}</p>
            <p class="text-xs text-white/85">{{ __('Sign-ins to this page are recorded.') }}</p>
        </div>
    </div>
@else
    <p class="mb-4 inline-flex items-center gap-2 rounded-full bg-maroon-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-maroon-800 dark:bg-stone-800 dark:text-stone-100">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM4 21a8 8 0 0116 0"/></svg>
        {{ __('Resident') }}
    </p>
@endif
