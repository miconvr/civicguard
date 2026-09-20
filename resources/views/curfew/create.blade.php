<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Log Curfew Violation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('curfew.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Minor's Name</label>
                        <input type="text" name="minor_name" value="{{ old('minor_name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('minor_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Age</label>
                        <input type="number" name="minor_age" value="{{ old('minor_age') }}" min="0" max="17" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('minor_age') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Guardian's Name</label>
                        <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Guardian's Contact Number</label>
                        <input type="text" name="guardian_contact" value="{{ old('guardian_contact') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Date & Time of Apprehension</label>
                        <input type="datetime-local" name="apprehension_datetime" value="{{ old('apprehension_datetime') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('apprehension_datetime') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Location</label>
                        <input type="text" name="apprehension_location" value="{{ old('apprehension_location') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('apprehension_location') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Number of Prior Violations</label>
                        <input type="number" name="prior_violations_count" value="{{ old('prior_violations_count', 0) }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('prior_violations_count') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">
                        Log Violation
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
