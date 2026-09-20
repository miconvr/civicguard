<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Tanod / Official Account
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

                <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="cg-label">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="cg-input">
                        @error('name') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="cg-input">
                        @error('email') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">Phone Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="cg-input">
                        @error('phone_number') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">Role</label>
                        <select name="role" class="cg-input">
                            <option value="tanod" {{ old('role') === 'tanod' ? 'selected' : '' }}>Tanod</option>
                            <option value="official" {{ old('role') === 'official' ? 'selected' : '' }}>Barangay Official</option>
                        </select>
                        @error('role') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">Temporary Password</label>
                        <input type="password" name="password" autocomplete="new-password" class="cg-input">
                        @error('password') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="cg-btn">Create Account</button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
