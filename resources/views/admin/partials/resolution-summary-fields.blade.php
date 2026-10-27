{{--
    Incident summary fields shown when a report is being resolved.
    Expects: $model (Alpine object with type, narrative, action, persons, followUp, followUpNotes),
    $showExpr (Alpine expression: true when status is "resolved"), $requiredExpr (Alpine expression:
    true when the fields are mandatory), $lockedExpr (true when a saved summary is shown read-only),
    $editingExpr (true while a saved summary is being edited), $unlockStmt (Alpine statement run by the
    Edit button), $idPrefix, optional $wrapperClass.
--}}
@php($types = \App\Models\ResolutionSummary::typeLabels())
<fieldset x-show="{{ $showExpr }}" x-cloak class="space-y-4 rounded-lg border border-gray-200 p-4 dark:border-gray-700 {{ $wrapperClass ?? '' }}">
    <legend class="px-1 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('Incident summary') }}</legend>
    <div class="flex flex-wrap items-center justify-between gap-2">
        <p x-show="!({{ $lockedExpr }})" class="text-xs cg-muted">{{ __('Seen by barangay officials and admins only. It is not shared with the resident.') }}</p>
        <p x-show="{{ $lockedExpr }}" x-cloak class="text-xs font-medium text-green-700 dark:text-green-400">{{ __('Saved and locked. Click Edit to change it.') }}</p>
        <button type="button" x-show="{{ $lockedExpr }}" x-cloak @click="{{ $unlockStmt }}" class="inline-flex min-h-11 items-center rounded-lg border border-gray-300 px-3 text-sm font-medium text-gray-800 transition hover:border-maroon-700 dark:border-gray-600 dark:text-gray-200">{{ __('Edit summary') }}</button>
    </div>
    <p x-show="{{ $editingExpr }}" x-cloak class="text-xs font-medium text-amber-700 dark:text-amber-400">{{ __('You are editing a saved summary. Save to lock it again.') }}</p>

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
        <div x-show="{{ $model }}.followUp" x-cloak>
            <label for="{{ $idPrefix }}-followup" class="sr-only">{{ __('Follow-up notes') }}</label>
            <textarea id="{{ $idPrefix }}-followup" name="summary_follow_up_notes" x-model="{{ $model }}.followUpNotes" :required="{{ $model }}.followUp && ({{ $requiredExpr }})" maxlength="1000" rows="2" class="cg-input w-full disabled:cursor-not-allowed disabled:opacity-70" :disabled="{{ $lockedExpr }}" placeholder="{{ __('What still needs to be done, and by whom?') }}"></textarea>
        </div>
    </div>
</fieldset>
