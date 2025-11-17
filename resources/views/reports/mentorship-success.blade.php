<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mentorship Success') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
                        <p class="text-2xl font-bold">{{ $data['total_mentorships'] }}</p>
                    </div>
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Active</p>
                        <p class="text-2xl font-bold">{{ $data['active_mentorships'] }}</p>
                    </div>
                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Completed</p>
                        <p class="text-2xl font-bold">{{ $data['completed_mentorships'] }}</p>
                    </div>
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Success Rate</p>
                        <p class="text-2xl font-bold">{{ $data['success_rate'] }}%</p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Average Duration: {{ $data['average_duration_days'] }} days
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

