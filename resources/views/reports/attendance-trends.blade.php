<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Attendance Trends') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Period: {{ $data['period']['start'] }} to {{ $data['period']['end'] }}
                    </p>
                    <p class="text-lg font-semibold mt-2">
                        Total Attendance: {{ $data['total_attendance'] }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Average per day: {{ $data['average_per_day'] }}
                    </p>
                </div>
                <div class="mt-4">
                    <h3 class="font-semibold mb-2">Daily Trends</h3>
                    <div class="space-y-2">
                        @foreach($data['trends'] as $trend)
                            <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                <span>{{ $trend['date'] }}</span>
                                <span class="font-semibold">{{ $trend['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

