@props(['current' => 'resident'])

<nav aria-label="{{ __('Choose login type') }}" class="mb-6 grid grid-cols-2 gap-1 rounded-lg bg-stone-100 p-1 dark:bg-stone-800">
    @foreach ([['resident', route('login'), __('Resident')], ['staff', route('staff.login'), __('Staff')]] as [$key, $url, $label])
        <a href="{{ $url }}"
           @if ($current === $key) aria-current="page" @endif
           class="flex min-h-[44px] items-center justify-center rounded-md px-3 text-sm font-semibold transition-colors {{ $current === $key ? 'bg-white text-maroon-800 shadow-sm dark:bg-stone-700 dark:text-white' : 'text-stone-600 hover:text-maroon-800 dark:text-stone-300 dark:hover:text-white' }}">
            {{ $label }}
        </a>
    @endforeach
</nav>
