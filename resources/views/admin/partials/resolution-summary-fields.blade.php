{{--
    Incident summary fields shown when a report is being resolved.
    Expects: $model (Alpine object with type, narrative, action, persons, followUp, followUpNotes),
    $showExpr (Alpine expression: true when status is "resolved"), $requiredExpr (Alpine expression:
    true when the fields are mandatory), $idPrefix, optional $wrapperClass.
--}}
@php($types = \App\Models\ResolutionSummary::typeLabels())
<fieldset x-show="{{ $showExpr }}" x-cloak class="space-y-4 rounded-lg border border-gray-200 p-4 dark:border-gray-700 {{ $wrapperClass ?? '' }}">
    <legend class="px-1 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('Incident summary') }}</legend>
    <p class="text-xs cg-muted">{{ __('Seen by barangay officials and admins only. It is not shared with the resident.') }}</p>

    <div class="space-y-2">
        <label for="{{ $idPrefix }}-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('How was it resolved?') }}</label>
        <select id="{{ $idPrefix }}-type" name="summary_resolution_type" x-model="{{ $model }}.type" :required="{{ $requiredExpr }}" class="cg-select w-full">
            <option value="">{{ __('Select an outcome') }}</option>
            @foreach ($types as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="space-y-2">
        <label for="{{ $idPrefix }}-narrative" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Summary of what happened') }}</label>
        <textarea id="{{ $idPrefix }}-narrative" name="summary_narrative" x-model="{{ $model }}.narrative" :required="{{ $requiredExpr }}" minlength="10" maxlength="3000" rows="4" class="cg-input w-full" placeholder="{{ __('What did you find on arrival, and what happened?') }}"></textarea>
    </div>

    <div class="space-y-2">
        <label for="{{ $idPrefix }}-action" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Action taken') }}</label>
        <textarea id="{{ $idPrefix }}-action" name="summary_action_taken" x-model="{{ $model }}.action" :required="{{ $requiredExpr }}" minlength="5" maxlength="2000" rows="3" class="cg-input w-full" placeholder="{{ __('What did you or the barangay do about it?') }}"></textarea>
    </div>

    <div class="space-y-2">
        <label for="{{ $idPrefix }}-persons" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Persons involved or witnesses (optional)') }}</label>
        <textarea id="{{ $idPrefix }}-persons" name="summary_persons_involved" x-model="{{ $model }}.persons" maxlength="1000" rows="2" class="cg-input w-full"></textarea>
    </div>

    <div class="space-y-2">
        <label class="flex min-h-11 items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-300">
            <input type="checkbox" name="summary_follow_up_needed" value="1" x-model="{{ $model }}.followUp" class="h-4 w-4 rounded border-gray-300 text-maroon-700 focus:ring-maroon-600">
            {{ __('Follow-up is still needed') }}
        </label>
        <div x-show="{{ $model }}.followUp" x-cloak>
            <label for="{{ $idPrefix }}-followup" class="sr-only">{{ __('Follow-up notes') }}</label>
            <textarea id="{{ $idPrefix }}-followup" name="summary_follow_up_notes" x-model="{{ $model }}.followUpNotes" :required="{{ $model }}.followUp && ({{ $requiredExpr }})" maxlength="1000" rows="2" class="cg-input w-full" placeholder="{{ __('What still needs to be done, and by whom?') }}"></textarea>
        </div>
    </div>
</fieldset>
