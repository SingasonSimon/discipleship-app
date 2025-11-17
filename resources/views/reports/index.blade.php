<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reports & Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('reports.attendance-trends') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-lg font-semibold mb-2">Attendance Trends</h3>
                    <p class="text-gray-600 dark:text-gray-400">View attendance patterns and trends over time</p>
                </a>

                <a href="{{ route('reports.member-engagement') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-lg font-semibold mb-2">Member Engagement</h3>
                    <p class="text-gray-600 dark:text-gray-400">Analyze member participation and engagement metrics</p>
                </a>

                <a href="{{ route('reports.class-performance') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-lg font-semibold mb-2">Class Performance</h3>
                    <p class="text-gray-600 dark:text-gray-400">Track class attendance rates and performance</p>
                </a>

                <a href="{{ route('reports.mentorship-success') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-lg font-semibold mb-2">Mentorship Success</h3>
                    <p class="text-gray-600 dark:text-gray-400">Monitor mentorship completion and success rates</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

