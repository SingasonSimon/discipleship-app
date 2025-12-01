<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Mentorship') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('mentorships.show', $mentorship) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    View Mentorship
                </a>
                <a href="{{ route('mentorships.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Mentorships
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg relative">
                <div class="p-6">
                    <form method="POST" action="{{ route('mentorships.update', $mentorship) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Member Selection -->
                        <div>
                            <label for="member_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Member <span class="text-red-500">*</span>
                            </label>
                            <select name="member_id" id="member_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select a member</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ old('member_id', $mentorship->member_id) == $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }} - {{ $member->phone }}
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mentor Selection -->
                        <div>
                            <label for="mentor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Mentor <span class="text-red-500">*</span>
                            </label>
                            <select name="mentor_id" id="mentor_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select a mentor</option>
                                @foreach($mentors as $mentor)
                                    <option value="{{ $mentor->id }}" {{ old('mentor_id', $mentorship->mentor_id) == $mentor->id ? 'selected' : '' }}>
                                        {{ $mentor->name }} ({{ ucfirst($mentor->role) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('mentor_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Start Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $mentorship->start_date->format('Y-m-d')) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration (in weeks) -->
                        <div>
                            <label for="duration_weeks" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Duration (in weeks)
                            </label>
                            @php
                                $durationWeeks = null;
                                if ($mentorship->start_date && $mentorship->end_date) {
                                    $durationWeeks = $mentorship->start_date->diffInWeeks($mentorship->end_date);
                                }
                            @endphp
                            <input type="number" name="duration_weeks" id="duration_weeks" min="1" max="104" value="{{ old('duration_weeks', $durationWeeks) }}"
                                   placeholder="e.g., 12 weeks"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional: Enter duration in weeks. End date will be calculated automatically.</p>
                            @error('duration_weeks')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                End Date
                            </label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $mentorship->end_date?->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional: Leave blank if using duration, or set a specific end date.</p>
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meeting Frequency -->
                        <div>
                            <label for="meeting_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Meeting Frequency
                            </label>
                            <select name="meeting_frequency" id="meeting_frequency"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select frequency</option>
                                <option value="weekly" {{ old('meeting_frequency', $mentorship->meeting_frequency) === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="biweekly" {{ old('meeting_frequency', $mentorship->meeting_frequency) === 'biweekly' ? 'selected' : '' }}>Bi-weekly</option>
                                <option value="monthly" {{ old('meeting_frequency', $mentorship->meeting_frequency) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                            @error('meeting_frequency')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="active" {{ old('status', $mentorship->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="paused" {{ old('status', $mentorship->status) === 'paused' ? 'selected' : '' }}>Paused</option>
                                <option value="completed" {{ old('status', $mentorship->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Notes
                            </label>
                            <textarea name="notes" id="notes" rows="4"
                                      placeholder="Additional notes about this mentorship relationship..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes', $mentorship->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('mentorships.show', $mentorship) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Mentorship
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
