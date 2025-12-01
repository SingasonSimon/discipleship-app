<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Message') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('messages.store') }}">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Message Type -->
                            <div>
                                <label for="message_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Message Type</label>
                                <select id="message_type" name="message_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="welcome">Welcome Message</option>
                                    <option value="class_reminder">Class Reminder</option>
                                    <option value="mentorship_assigned">Mentorship Assigned</option>
                                    <option value="general">General Message</option>
                                    <option value="custom">Custom Message</option>
                                </select>
                                @error('message_type')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Channel -->
                            <div>
                                <label for="channel" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Channel</label>
                                <select id="channel" name="channel" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="email">Email</option>
                                </select>
                                @error('channel')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="mt-6">
                            <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                            <input type="text" id="subject" name="subject" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" value="{{ old('subject') }}">
                            @error('subject')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message Content -->
                        <div class="mt-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Message Content</label>
                            <textarea id="content" name="content" rows="8" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Recipients -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recipients</label>
                            <div class="mt-2 space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="recipients[]" value="all_members" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">All Members</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="recipients[]" value="class_members" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Class Members</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="recipients[]" value="mentorship_pairs" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Mentorship Pairs</span>
                                </label>
                            </div>
                            @error('recipients')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Scheduling -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="schedule_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Schedule</label>
                                <select id="schedule_type" name="schedule_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="immediate">Send Immediately</option>
                                    <option value="scheduled">Schedule for Later</option>
                                </select>
                            </div>

                            <div id="scheduled_at_field" class="hidden">
                                <label for="scheduled_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scheduled Date & Time</label>
                                <input type="datetime-local" id="scheduled_at" name="scheduled_at" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('scheduled_at')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('messages.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <x-loading-button type="submit" class="bg-blue-500 hover:bg-blue-700">
                                <span id="submit-text">Create Message</span>
                            </x-loading-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('schedule_type').addEventListener('change', function() {
            const scheduledAtField = document.getElementById('scheduled_at_field');
            const submitText = document.getElementById('submit-text');
            if (this.value === 'scheduled') {
                scheduledAtField.classList.remove('hidden');
                if (submitText) submitText.textContent = 'Schedule Message';
            } else {
                scheduledAtField.classList.add('hidden');
                if (submitText) submitText.textContent = 'Send Message';
            }
        });
        
        // Update button text on page load
        document.addEventListener('DOMContentLoaded', function() {
            const scheduleType = document.getElementById('schedule_type');
            const submitText = document.getElementById('submit-text');
            if (scheduleType && submitText) {
                if (scheduleType.value === 'immediate') {
                    submitText.textContent = 'Send Message';
                } else {
                    submitText.textContent = 'Schedule Message';
                }
            }
        });
    </script>
</x-app-layout>

