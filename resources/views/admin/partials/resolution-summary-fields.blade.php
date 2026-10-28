{{--
    Incident summary fields shown when a report is being resolved.
    Expects: $model (Alpine object with type, narrative, action, persons, followUp, followUpNotes),
    $showExpr (Alpine expression: true when status is "resolved"), $requiredExpr (Alpine expression:
    true when the fields are mandatory), $lockedExpr (true when a saved summary is shown read-only),
    $editingExpr (true while a saved summary is being edited), $unlockStmt (Alpine statement run by the
    Edit button), $idPrefix, optional $wrapperClass.
--}}
@php($types = \App\Models\ResolutionSummary::typeLabels())
<fieldset x-show="{{ $showExpr }}" x-cloak
    x-transition:enter="transition ease-[var(--cg-ease-out)] duration-[var(--cg-motion-enter)]"
    x-transition:enter-start="opacity-0 -translate-y-1"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-[var(--cg-ease-in)] duration-[var(--cg-motion-exit)]"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="cg-summary-fieldset space-y-4 rounded-xl border border-gray-200 p-4 transition-colors duration-[var(--cg-motion-interaction)] ease-[var(--cg-ease-out)] hover:border-maroon-700/30 focus-within:border-maroon-700/40 dark:border-gray-700 dark:hover:border-white/25 dark:focus-within:border-white/30 {{ $wrapperClass ?? '' }}">
    <legend class="px-1 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('Incident summary') }}</legend>

    {{-- Status banner: one row on wide screens, stacked on narrow ones --}}
    <div class="min-h-11">
        <p x-show="!({{ $lockedExpr }}) && !({{ $editingExpr }})" class="text-xs leading-5 cg-muted">{{ __('Seen by barangay officials and admins only. It is not shared with the resident.') }}</p>

        <div x-show="{{ $lockedExpr }}" x-cloak
            x-transition:enter="transition ease-[var(--cg-ease-out)] duration-[var(--cg-motion-enter)]"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="flex flex-col gap-3 rounded-lg bg-green-50 px-3 py-2.5 sm:flex-row sm:items-center sm:justify-between dark:bg-green-900/20">
            <p class="flex items-start gap-2 text-sm font-medium leading-5 text-green-800 dark:text-green-300">
                <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd"/></svg>
                <span>{{ __('Saved and locked. Click Edit to change it.') }}</span>
            </p>
            <button type="button" @click="{{ $unlockStmt }}" class="cg-btn-secondary cg-motion-lift w-full shrink-0 sm:w-auto">{{ __('Edit summary') }}</button>
        </div>

        <p x-show="{{ $editingExpr }}" x-cloak
            x-transition:enter="transition ease-[var(--cg-ease-out)] duration-[var(--cg-motion-enter)]"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="flex items-start gap-2 rounded-lg bg-amber-50 px-3 py-2.5 text-sm font-medium leading-5 text-amber-800 dark:bg-amber-900/20 dark:text-amber-300">
            <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z"/><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z"/></svg>
            <span>{{ __('You are editing a saved summary. Save to lock it again.') }}</span>
        </p>
    </div>

    <div class="space-y-2">
        <label for="{{ $idPrefix }}-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('How was it resolved?') }}</label>
        <select id="{{ $idPrefix }}-type" name="summary_resolution_type" x-model="{{ $model }}.type" :required="{{ $requiredExpr }}" class="cg-select w-full disabled:cursor-not-allowed disabled:opacity-70" :disabled="{{ $lockedExpr }}">
            <option value="">{{ __('Select an outcome') }}</option>
            @foreach ($types as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="space-y-2">
        <label for="{{ $idPrefix }}-narrative" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Summary of what happened') }}</label>
        <textarea id="{{ $idPrefix }}-narrative" name="summary_narrative" x-model="{{ $model }}.narrative" :required="{{ $requiredExpr }}" minlength="10" maxlength="3000" rows="4" class="cg-input w-full disabled:cursor-not-allowed disabled:opacity-70" :disabled="{{ $lockedExpr }}" placeholder="{{ __('What did you find on arrival, and what happened?') }}"></textarea>
    </div>

    <div class="space-y-2">
        <label for="{{ $idPrefix }}-action" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Action taken') }}</label>
        <textarea id="{{ $idPrefix }}-action" name="summary_action_taken" x-model="{{ $model }}.action" :required="{{ $requiredExpr }}" minlength="5" maxlength="2000" rows="3" class="cg-input w-full disabled:cursor-not-allowed disabled:opacity-70" :disabled="{{ $lockedExpr }}" placeholder="{{ __('What did you or the barangay do about it?') }}"></textarea>
    </div>

    <div class="space-y-2">
        <label for="{{ $idPrefix }}-persons" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Persons involved or witnesses (optional)') }}</label>
        <textarea id="{{ $idPrefix }}-persons" name="summary_persons_involved" x-model="{{ $model }}.persons" maxlength="1000" rows="2" class="cg-input w-full disabled:cursor-not-allowed disabled:opacity-70" :disabled="{{ $lockedExpr }}"></textarea>
    </div>

    <div class="space-y-2">
        <label class="flex min-h-11 items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-300">
            <input type="checkbox" name="summary_follow_up_needed" value="1" x-model="{{ $model }}.followUp" class="h-4 w-4 rounded border-gray-300 text-maroon-700 focus:ring-maroon-600" :disabled="{{ $lockedExpr }}">
            {{ __('Follow-up is still needed') }}
        </label>
        <div x-show="{{ $model }}.followUp" x-cloak
            x-transition:enter="transition ease-[var(--cg-ease-out)] duration-[var(--cg-motion-enter)]"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0">
            <label for="{{ $idPrefix }}-followup" class="sr-only">{{ __('Follow-up notes') }}</label>
            <textarea id="{{ $idPrefix }}-followup" name="summary_follow_up_notes" x-model="{{ $model }}.followUpNotes" :required="{{ $model }}.followUp && ({{ $requiredExpr }})" maxlength="1000" rows="2" class="cg-input w-full disabled:cursor-not-allowed disabled:opacity-70" :disabled="{{ $lockedExpr }}" placeholder="{{ __('What still needs to be done, and by whom?') }}"></textarea>
        </div>
    </div>
</fieldset>
