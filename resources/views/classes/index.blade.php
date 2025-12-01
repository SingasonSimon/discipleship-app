<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Discipleship Classes') }}
            </h2>
            @can('create', App\Models\DiscipleshipClass::class)
                <a href="{{ route('classes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Class
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('classes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   placeholder="Class title, description, or mentor..." 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        
                        <div>
                            <label for="mentor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mentor</label>
                            <select name="mentor_id" id="mentor_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">All Mentors</option>
                                @foreach($mentors as $mentor)
                                    <option value="{{ $mentor->id }}" {{ request('mentor_id') == $mentor->id ? 'selected' : '' }}>
                                        {{ $mentor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select name="status" id="status" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="schedule_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Schedule Type</label>
                            <select name="schedule_type" id="schedule_type" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">All Types</option>
                                <option value="weekly" {{ request('schedule_type') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="biweekly" {{ request('schedule_type') === 'biweekly' ? 'selected' : '' }}>Biweekly</option>
                                <option value="monthly" {{ request('schedule_type') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="custom" {{ request('schedule_type') === 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-4 flex justify-end space-x-2">
                            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Filter
                            </button>
                            <a href="{{ route('classes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Classes Grid -->
            @if($classes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($classes as $class)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ $class->title }}
                                    </h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($class->is_active) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                        @endif">
                                        {{ $class->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                                    {{ $class->description }}
                                </p>

                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Mentor:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ $class->mentor->name }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Capacity:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ $class->capacity }} members</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Duration:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ $class->duration_weeks }} weeks</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Schedule:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ ucfirst($class->schedule_type) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Sessions:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ $class->sessions->count() }}</span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Created {{ $class->created_at->diffForHumans() }}
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('classes.show', $class) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm">
                                            View
                                        </a>
                                        @auth
                                            @if(auth()->user()->isMember())
                                                @php
                                                    $member = \App\Models\Member::where('user_id', auth()->id())->first();
                                                    $isEnrolled = $member && $member->isEnrolledInClass($class);
                                                    $hasActiveEnrollment = $member && $member->hasActiveEnrollment();
                                                @endphp
                                                @if(!$isEnrolled && !$hasActiveEnrollment && !$class->isFull() && $class->is_active)
                                                    <a href="{{ route('member.enrollments.create', $class) }}" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 text-sm">
                                                        Enroll
                                                    </a>
                                                @endif
                                            @endif
                                        @endauth
                                        @if(auth()->user()->isAdmin() || auth()->user()->isPastor())
                                            @can('update', $class)
                                                <a href="{{ route('classes.edit', $class) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 text-sm">
                                                    Edit
                                                </a>
                                            @endcan
                                        @endif
                                        @if(auth()->user()->isAdmin())
                                            @can('delete', $class)
                                                <form method="POST" action="{{ route('classes.destroy', $class) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this class?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $classes->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No classes found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new discipleship class.</p>
                        @can('create', App\Models\DiscipleshipClass::class)
                            <div class="mt-6">
                                <a href="{{ route('classes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Create Class
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
