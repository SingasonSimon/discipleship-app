<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Member Engagement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Members</p>
                        <p class="text-2xl font-bold">{{ $data['total_members'] }}</p>
                    </div>
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded">
                        <p class="text-sm text-gray-600 dark:text-gray-400">With Attendance</p>
                        <p class="text-2xl font-bold">{{ $data['members_with_attendance'] }}</p>
                    </div>
                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded">
                        <p class="text-sm text-gray-600 dark:text-gray-400">In Classes</p>
                        <p class="text-2xl font-bold">{{ $data['members_in_classes'] }}</p>
                    </div>
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Engagement Rate</p>
                        <p class="text-2xl font-bold">{{ $data['engagement_rate'] }}%</p>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Top Engaged Members</h3>
                    <div class="space-y-2">
                        @foreach($data['top_engaged_members'] as $member)
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                <span>{{ $member['name'] }}</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    Engagement: {{ $member['total_engagement'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

