<x-app-layout>
    <x-slot name="motion">off</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Log Curfew Violation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card">

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/30 px-4 py-3 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('curfew.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="cf_minor_name" class="cg-label">Minor's Name</label>
                        <input id="cf_minor_name" type="text" name="minor_name" required value="{{ old('minor_name') }}" class="cg-input">
                        @error('minor_name') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="cf_minor_age" class="cg-label">Age</label>
                        <input id="cf_minor_age" type="number" name="minor_age" value="{{ old('minor_age') }}" min="0" max="17" class="cg-input">
                        @error('minor_age') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="cf_guardian_name" class="cg-label">Guardian's Name</label>
                        <input id="cf_guardian_name" type="text" name="guardian_name" value="{{ old('guardian_name') }}" class="cg-input">
                    </div>

                    <div>
                        <label for="cf_guardian_contact" class="cg-label">Guardian's Contact Number</label>
                        <input id="cf_guardian_contact" type="text" name="guardian_contact" value="{{ old('guardian_contact') }}" class="cg-input">
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="guardian_notified" value="1" {{ old('guardian_notified') ? 'checked' : '' }}>
                        Guardian notified
                    </label>

                    <div>
                        <label for="cf_referral_action" class="cg-label">Referral or Follow-up Action</label>
                        <select id="cf_referral_action" name="referral_action" class="cg-input">
                            <option value="">Select action...</option>
                            <option value="Released to guardian" {{ old('referral_action') === 'Released to guardian' ? 'selected' : '' }}>Released to guardian</option>
                            <option value="Referred to social-welfare personnel" {{ old('referral_action') === 'Referred to social-welfare personnel' ? 'selected' : '' }}>Referred to social-welfare personnel</option>
                            <option value="Referred to barangay official" {{ old('referral_action') === 'Referred to barangay official' ? 'selected' : '' }}>Referred to barangay official</option>
                            <option value="Other" {{ old('referral_action') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('referral_action') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">Record safeguarding actions factually and follow current barangay and social-welfare procedures for minors.</p>

                    <fieldset class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                        <legend class="px-1 text-sm font-semibold text-gray-900 dark:text-gray-100">Apprehension Details</legend>
                        <label for="cf_apprehension_datetime" class="cg-label">Date and time of apprehension</label>
                        <input id="cf_apprehension_datetime" type="datetime-local" name="apprehension_datetime" required value="{{ old('apprehension_datetime') }}" class="cg-input w-full min-w-0">
                        <p class="mt-2 text-xs leading-5 text-gray-600 dark:text-gray-400">Select when the incident occurred. Check the date and time before saving.</p>
                        @error('apprehension_datetime') <p class="cg-error" role="alert">{{ $message }}</p> @enderror
                    </fieldset>

                    <div>
                        <label for="cf_apprehension_location" class="cg-label">Location</label>
                        <input id="cf_apprehension_location" type="text" name="apprehension_location" required value="{{ old('apprehension_location') }}" class="cg-input">
                        @error('apprehension_location') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="cf_prior_violations_count" class="cg-label">Number of Prior Violations</label>
                        <input id="cf_prior_violations_count" type="number" name="prior_violations_count" required value="{{ old('prior_violations_count', 0) }}" min="0" class="cg-input">
                        @error('prior_violations_count') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="cf_notes" class="cg-label">Notes</label>
                        <textarea id="cf_notes" name="notes" rows="3" class="cg-input">{{ old('notes') }}</textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="cg-btn w-full sm:w-auto" data-loading-btn data-loading-text="{{ __('Logging...') }}">{{ __('Log Violation') }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>