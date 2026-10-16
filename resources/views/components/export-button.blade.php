<a data-cg-export {{ $attributes->merge(['class' => 'inline-flex min-h-[40px] items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 cg-surface-raised dark:bg-transparent px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm transition hover:border-maroon-700 hover:text-maroon-700 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-maroon-600']) }}>
    <svg data-export-icon class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
    </svg>
    <svg data-export-spinner class="hidden h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
    </svg>
    <span data-export-label>{{ __('Export PDF') }}</span>
</a>

@once
    <script>
        (function () {
            if (window.__cgExportInit) return;
            window.__cgExportInit = true;

            const text = {
                busy: @json(__('Preparing…')),
                failed: @json(__('Export failed')),
            };

            document.addEventListener('click', async function (event) {
                const link = event.target.closest('a[data-cg-export]');
                if (!link || event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.button !== 0) return;
                event.preventDefault();
                if (link.dataset.busy === '1') return;

                const icon = link.querySelector('[data-export-icon]');
                const spinner = link.querySelector('[data-export-spinner]');
                const label = link.querySelector('[data-export-label]');
                const original = label.textContent;
                const show = (busy, message) => {
                    link.dataset.busy = busy ? '1' : '0';
                    link.setAttribute('aria-busy', busy ? 'true' : 'false');
                    icon.classList.toggle('hidden', busy);
                    spinner.classList.toggle('hidden', !busy);
                    label.textContent = message || original;
                };

                show(true, text.busy);

                try {
                    const response = await fetch(link.href, { credentials: 'same-origin' });
                    if (!response.ok) throw new Error('HTTP ' + response.status);

                    const blob = await response.blob();
                    const match = /filename\*?=(?:UTF-8'')?"?([^";]+)"?/i.exec(response.headers.get('Content-Disposition') || '');
                    const anchor = document.createElement('a');
                    anchor.href = URL.createObjectURL(blob);
                    anchor.download = match ? decodeURIComponent(match[1]) : 'export';
                    document.body.appendChild(anchor);
                    anchor.click();
                    anchor.remove();
                    setTimeout(function () { URL.revokeObjectURL(anchor.href); }, 10000);
                    show(false);
                } catch (error) {
                    show(false, text.failed + ' (' + (error.message || 'error') + ')');
                    setTimeout(function () { label.textContent = original; }, 6000);
                }
            });
        })();
    </script>
@endonce
