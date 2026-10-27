{{-- Brief green check shown to the right of a Save button. `when` is an Alpine expression. --}}
@props(['when' => 'false'])
<span x-show="{{ $when }}" x-cloak role="status" class="inline-flex items-center gap-1.5 text-sm font-medium text-green-700 dark:text-green-400">
    <span class="t-success-check shrink-0" data-state="in" aria-hidden="true">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="m5 12.5 4 4L19 7" />
        </svg>
    </span>
    <span>{{ __('Saved') }}</span>
</span>
