@props(['current' => 'resident'])

<nav aria-label="{{ __('Choose login type') }}" class="mb-6 grid grid-cols-2 gap-1 rounded-lg bg-stone-50 p-1 ring-1 ring-stone-300 dark:bg-stone-800 dark:ring-stone-600">
    @foreach ([['resident', route('login'), __('Resident')], ['staff', route('staff.login'), __('Staff')]] as [$key, $url, $label])
        <a href="{{ $url }}"
           @if ($current === $key) aria-current="page" @endif
           class="flex min-h-[44px] items-center justify-center rounded-md px-3 text-sm transition-colors {{ $current === $key ? 'bg-maroon-800 font-bold text-white shadow-sm dark:bg-maroon-700' : 'font-semibold text-stone-700 hover:text-maroon-800 dark:text-stone-300 dark:hover:text-white' }}">
            {{ $label }}
        </a>
    @endforeach
</nav>
