<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Mentorship Details') }}
            </h2>
            <div class="space-x-2">
                @if(!auth()->user()->isPastor())
                    <a href="{{ route('mentorships.edit', $mentorship) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                @endif
                <a href="{{ route('mentorships.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Mentorships
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Mentorship Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Member Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Member Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</label>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $mentorship->member->full_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $mentorship->member->phone }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $mentorship->member->email ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Address</label>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $mentorship->member->address ?? 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Mentor Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Mentor Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Name</label>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $mentorship->mentor->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Role</label>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($mentorship->mentor->role) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $mentorship->mentor->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mentorship Details -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Mentorship Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Start Date</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $mentorship->start_date->format('F d, Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">End Date</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $mentorship->end_date ? $mentorship->end_date->format('F d, Y') : 'Not set' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($mentorship->status === 'active') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                    @elseif($mentorship->status === 'completed') bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                                    @elseif($mentorship->status === 'paused') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                    @endif">
                                    {{ ucfirst($mentorship->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Meeting Frequency</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $mentorship->meeting_frequency ? ucfirst(str_replace('-', ' ', $mentorship->meeting_frequency)) : 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Duration</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $stats['duration_days'] }} days</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Created At</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $mentorship->created_at->format('F d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($mentorship->notes)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Notes</label>
                            <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                                <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $mentorship->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['duration_days'] }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Days Active</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['meetings_held'] }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Meetings Held</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $stats['last_meeting'] ? $stats['last_meeting']->format('M d') : 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Last Meeting</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if(!auth()->user()->isPastor())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Actions</h3>
                        <div class="flex flex-wrap gap-3">
                            <!-- Status Update -->
                            @if($mentorship->status === 'active')
                                <form method="POST" action="{{ route('mentorships.updateStatus', $mentorship) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="paused">
                                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                        Pause Mentorship
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('mentorships.updateStatus', $mentorship) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Complete Mentorship
                                    </button>
                                </form>
                            @elseif($mentorship->status === 'paused')
                                <form method="POST" action="{{ route('mentorships.updateStatus', $mentorship) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Resume Mentorship
                                    </button>
                                </form>
                            @endif

                            <!-- Delete -->
                            <form method="POST" action="{{ route('mentorships.destroy', $mentorship) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this mentorship? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Delete Mentorship
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
