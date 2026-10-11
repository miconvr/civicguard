<nav class="flex gap-2 border-b border-stone-200 dark:border-[#3d3d3a]" aria-label="{{ __('Report views') }}">
    <a href="{{ route('admin.analytics') }}"
       @class([
           'inline-flex min-h-11 items-center border-b-2 px-3 text-sm font-semibold transition',
           'border-maroon-700 text-maroon-800 dark:border-[#c27872] dark:text-[#c27872]' => $activeTab === 'analytics',
           'border-transparent text-stone-600 hover:border-stone-300 hover:text-stone-900 dark:text-gray-400 dark:hover:border-gray-500 dark:hover:text-gray-100' => $activeTab !== 'analytics',
       ])
       @if ($activeTab === 'analytics') aria-current="page" @endif>
        {{ __('Analytics') }}
    </a>
    <a href="{{ route('admin.consolidatedReports') }}"
       @class([
           'inline-flex min-h-11 items-center border-b-2 px-3 text-sm font-semibold transition',
           'border-maroon-700 text-maroon-800 dark:border-[#c27872] dark:text-[#c27872]' => $activeTab === 'summary',
           'border-transparent text-stone-600 hover:border-stone-300 hover:text-stone-900 dark:text-gray-400 dark:hover:border-gray-500 dark:hover:text-gray-100' => $activeTab !== 'summary',
       ])
       @if ($activeTab === 'summary') aria-current="page" @endif>
        {{ __('Summary') }}
    </a>
</nav>
