<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Member') }}
            </h2>
            <a href="{{ route('members.show', $member) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Member
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('members.update', $member) }}">
                        @csrf
                        @method('PUT')

                        <!-- Full Name -->
                        <div class="mb-4">
                            <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $member->full_name) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('full_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $member->phone) }}" required
                                   placeholder="+254712345678 or 0712345678"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter a valid Kenyan phone number</p>
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Email Address
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $member->email) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date of Conversion -->
                        <div class="mb-4">
                            <label for="date_of_conversion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Date of Conversion <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date_of_conversion" id="date_of_conversion" value="{{ old('date_of_conversion', $member->date_of_conversion->format('Y-m-d')) }}" required
                                   max="{{ date('Y-m-d') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('date_of_conversion')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preferred Contact -->
                        <div class="mb-4">
                            <label for="preferred_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Preferred Contact Method <span class="text-red-500">*</span>
                            </label>
                            <select name="preferred_contact" id="preferred_contact" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select contact method</option>
                                <option value="email" {{ old('preferred_contact', $member->preferred_contact) === 'email' ? 'selected' : '' }}>Email</option>
                                <option value="call" {{ old('preferred_contact', $member->preferred_contact) === 'call' ? 'selected' : '' }}>Phone Call</option>
                            </select>
                            @error('preferred_contact')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Notes
                            </label>
                            <textarea name="notes" id="notes" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                      placeholder="Any additional notes about this member...">{{ old('notes', $member->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('members.show', $member) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <x-loading-button type="submit" class="bg-blue-500 hover:bg-blue-700">
                                Update Member
                            </x-loading-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
