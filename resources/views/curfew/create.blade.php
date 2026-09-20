<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Log Curfew Violation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card p-8">

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-700 bg-green-50 dark:bg-green-900/30 dark:text-green-300 px-4 py-3 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('curfew.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="cg-label">Minor's Name</label>
                        <input type="text" name="minor_name" value="{{ old('minor_name') }}" class="cg-input">
                        @error('minor_name') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">Age</label>
                        <input type="number" name="minor_age" value="{{ old('minor_age') }}" min="0" max="17" class="cg-input">
                        @error('minor_age') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">Guardian's Name</label>
                        <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" class="cg-input">
                        @error('guardian_name') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">Guardian's Contact Number</label>
                        <input type="text" name="guardian_contact" value="{{ old('guardian_contact') }}" class="cg-input">
                        @error('guardian_contact') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">Date &amp; Time of Apprehension</label>
                        <input type="datetime-local" name="apprehension_datetime" value="{{ old('apprehension_datetime') }}" class="cg-input">
                        @error('apprehension_datetime') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">Location</label>
                        <input type="text" name="apprehension_location" value="{{ old('apprehension_location') }}" class="cg-input">
                        @error('apprehension_location') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">Number of Prior Violations</label>
                        <input type="number" name="prior_violations_count" value="{{ old('prior_violations_count', 0) }}" min="0" class="cg-input">
                        @error('prior_violations_count') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">Notes</label>
                        <textarea name="notes" rows="3" class="cg-input">{{ old('notes') }}</textarea>
                        @error('notes') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="cg-btn">Log Violation</button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
