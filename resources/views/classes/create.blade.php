<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create New Class') }}
            </h2>
            <a href="{{ route('classes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Classes
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('classes.store') }}">
                        @csrf

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Class Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" id="description" rows="4" required
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                      placeholder="Describe the purpose and content of this discipleship class...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Mentor -->
                            <div class="mb-4">
                                <label for="mentor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Mentor <span class="text-red-500">*</span>
                                </label>
                                <select name="mentor_id" id="mentor_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select a mentor</option>
                                    @foreach($mentors as $mentor)
                                        <option value="{{ $mentor->id }}" {{ old('mentor_id') == $mentor->id ? 'selected' : '' }}>
                                            {{ $mentor->name }} ({{ ucfirst($mentor->role) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('mentor_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Capacity -->
                            <div class="mb-4">
                                <label for="capacity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Capacity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="capacity" id="capacity" value="{{ old('capacity') }}" required min="1" max="100"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('capacity')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Duration -->
                            <div class="mb-4">
                                <label for="duration_weeks" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Duration (weeks) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="duration_weeks" id="duration_weeks" value="{{ old('duration_weeks') }}" required min="1" max="52"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                       onchange="updateWeeklyContentFields()">
                                @error('duration_weeks')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Schedule Type -->
                            <div class="mb-4">
                                <label for="schedule_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Schedule Type <span class="text-red-500">*</span>
                                </label>
                                <select name="schedule_type" id="schedule_type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select schedule type</option>
                                    <option value="weekly" {{ old('schedule_type') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="biweekly" {{ old('schedule_type') === 'biweekly' ? 'selected' : '' }}>Biweekly</option>
                                    <option value="monthly" {{ old('schedule_type') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="custom" {{ old('schedule_type') === 'custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                                @error('schedule_type')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Schedule Day -->
                            <div class="mb-4" id="schedule_day_container" style="display: none;">
                                <label for="schedule_day" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Schedule Day
                                </label>
                                <select name="schedule_day" id="schedule_day"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select day</option>
                                    <option value="monday" {{ old('schedule_day') === 'monday' ? 'selected' : '' }}>Monday</option>
                                    <option value="tuesday" {{ old('schedule_day') === 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                                    <option value="wednesday" {{ old('schedule_day') === 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                                    <option value="thursday" {{ old('schedule_day') === 'thursday' ? 'selected' : '' }}>Thursday</option>
                                    <option value="friday" {{ old('schedule_day') === 'friday' ? 'selected' : '' }}>Friday</option>
                                    <option value="saturday" {{ old('schedule_day') === 'saturday' ? 'selected' : '' }}>Saturday</option>
                                    <option value="sunday" {{ old('schedule_day') === 'sunday' ? 'selected' : '' }}>Sunday</option>
                                </select>
                                @error('schedule_day')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Schedule Time -->
                            <div class="mb-4">
                                <label for="schedule_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Schedule Time <span class="text-red-500">*</span>
                                </label>
                                <input type="time" name="schedule_time" id="schedule_time" value="{{ old('schedule_time') }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('schedule_time')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Start Date -->
                            <div class="mb-4">
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Start Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                                       min="{{ date('Y-m-d') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Date -->
                            <div class="mb-4">
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    End Date
                                </label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="mb-6">
                            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Location
                            </label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}"
                                   placeholder="e.g., Main Hall, Room 101, Online"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('location')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                    Active (class is currently running)
                                </label>
                            </div>
                        </div>

                        <!-- Class Outline -->
                        <div class="mb-6">
                            <label for="class_outline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Class Outline
                            </label>
                            <textarea name="class_outline" id="class_outline" rows="6"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Provide an overview of the class curriculum, learning objectives, and what students will achieve by the end of the class.">{{ old('class_outline') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This outline will be saved as the first content item (type: Class Outline) and can be accessed by enrolled members.</p>
                        </div>

                        <!-- Weekly Content Planning -->
                        <div class="mb-6" id="weekly_content_section">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Class Content Planning (Weekly Topics)
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Plan the topics and content for each week of the class. Sessions will be automatically generated based on your schedule.
                            </p>
                            <p class="text-sm text-blue-600 dark:text-blue-400 mb-4">
                                💡 <strong>Tip:</strong> After creating the class, use the "Manage Content" button to add detailed lessons, assignments, resources, homework, and more!
                            </p>
                            <div id="weekly_content_container" class="space-y-4">
                                <!-- Weekly content fields will be dynamically generated here -->
                            </div>
                            <div class="mt-4">
                                <button type="button" onclick="updateWeeklyContentFields()" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    Refresh Fields
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('classes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Class
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/hide schedule day based on schedule type
        document.getElementById('schedule_type').addEventListener('change', function() {
            const scheduleDayContainer = document.getElementById('schedule_day_container');
            const scheduleDay = document.getElementById('schedule_day');
            
            if (this.value === 'weekly' || this.value === 'biweekly') {
                scheduleDayContainer.style.display = 'block';
                scheduleDay.required = true;
            } else {
                scheduleDayContainer.style.display = 'none';
                scheduleDay.required = false;
                scheduleDay.value = '';
            }
        });

        // Set initial state
        document.addEventListener('DOMContentLoaded', function() {
            const scheduleType = document.getElementById('schedule_type');
            if (scheduleType.value) {
                scheduleType.dispatchEvent(new Event('change'));
            }
            
            // Initialize weekly content fields on page load
            updateWeeklyContentFields();
        });
    </script>
</x-app-layout>
