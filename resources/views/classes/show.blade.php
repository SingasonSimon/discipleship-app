<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $class->title }}
            </h2>
            <div class="flex space-x-2">
                @if(auth()->user()->isAdmin() || auth()->user()->isPastor())
                    @can('update', $class)
                        <a href="{{ route('classes.edit', $class) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                    @endcan
                @endif
                @if(auth()->user()->isAdmin() || auth()->user()->isPastor())
                    @can('manageSessions', $class)
                        <a href="{{ route('classes.content.index', $class) }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                            Manage Content
                        </a>
                        <a href="{{ route('classes.sessions.create', $class) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Add Session
                        </a>
                    @endcan
                @endif
                <a href="{{ route('classes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Classes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Class Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Class Information</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($class->is_active) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                            @endif">
                            {{ $class->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $class->description }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Mentor</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $class->mentor->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Capacity</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $class->capacity }} members</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $class->duration_weeks }} weeks</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Schedule</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ ucfirst($class->schedule_type) }}
                                        @if($class->schedule_day)
                                            on {{ ucfirst($class->schedule_day) }}s
                                        @endif
                                        at {{ $class->schedule_time }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $class->location ?? 'Not specified' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Date</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $class->start_date->format('F d, Y') }}</dd>
                        </div>
                        @if($class->end_date)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">End Date</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $class->end_date->format('F d, Y') }}</dd>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Class Statistics -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Class Statistics</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_sessions'] }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Sessions</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['upcoming_sessions'] }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Upcoming Sessions</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_attendance'] }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Attendance</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $stats['average_attendance'] }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Avg. Attendance</div>
                        </div>
                    </div>

                    @if(isset($memberEnrollment))
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="text-center border border-green-200 dark:border-green-800 rounded-lg p-4 bg-green-50 dark:bg-green-900/20">
                                <div class="text-3xl font-bold text-green-600">
                                    {{ $memberEnrollment->completed_lessons }} / {{ max($totalPublishedLessons, 1) }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">Lessons Completed</div>
                            </div>
                            <div class="text-center border border-indigo-200 dark:border-indigo-800 rounded-lg p-4 bg-indigo-50 dark:bg-indigo-900/20">
                                <div class="text-3xl font-bold text-indigo-600">
                                    {{ number_format($memberEnrollment->attendance_rate, 2) }}%
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">Your Attendance / Progress Rate</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Sessions -->
            @php
                // Check if member is enrolled and can access content
                $member = auth()->user()->isMember() ? \App\Models\Member::where('user_id', auth()->id())->first() : null;
                $isEnrolled = $member && $member->isEnrolledInClass($class);
                $canAccessContent = $isEnrolled || auth()->user()->canManageClasses() || auth()->user()->id === $class->mentor_id;
            @endphp
            @if($recentSessions->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                @if($canAccessContent)
                                    Recent Sessions
                                @else
                                    Recent Sessions (Enroll to Access)
                                @endif
                            </h3>
                            @if($canAccessContent)
                                <a href="{{ route('classes.sessions.index', $class) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm">
                                    View All Sessions
                                </a>
                            @endif
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Topic</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Location</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Attendance</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($recentSessions as $session)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $session->session_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $session->topic }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $session->location ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $session->attendance->count() }} members
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    @if($canAccessContent)
                                                        <a href="{{ route('sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                            View
                                                        </a>
                                                    @else
                                                        <span class="text-gray-400 cursor-not-allowed" title="Enroll in class to access sessions">
                                                            View
                                                        </span>
                                                    @endif
                                                    @can('viewAttendance', $class)
                                                        <a href="{{ route('sessions.attendance', $session) }}" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                            Attendance
                                                        </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 9l6-6m0 0v6m0-6h-6" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No sessions yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating the first session for this class.</p>
                        @can('manageSessions', $class)
                            <div class="mt-6">
                                <a href="{{ route('classes.sessions.create', $class) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Create First Session
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            @endif

            <!-- Member Enrollment Section -->
            @auth
                @if(auth()->user()->isMember())
                    @php
                        $member = \App\Models\Member::where('user_id', auth()->id())->first();
                        $enrollment = $member ? $member->enrollments()->where('class_id', $class->id)->first() : null;
                        $isEnrolled = $member && $member->isEnrolledInClass($class);
                        $hasPending = $member && $member->hasPendingEnrollment($class);
                        $hasActiveEnrollment = $member && $member->hasActiveEnrollment();
                        $activeEnrollment = $hasActiveEnrollment ? $member->getActiveEnrollment() : null;
                    @endphp
                    
                    @if($member)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                @if($hasActiveEnrollment && $activeEnrollment && $activeEnrollment->class_id !== $class->id)
                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded p-4 mb-4">
                                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                            <strong>Active Enrollment:</strong> You currently have an active enrollment in "{{ $activeEnrollment->class->title }}". 
                                            You can only enroll in one class at a time. Please complete or cancel your current enrollment before enrolling in another class.
                                        </p>
                                        <div class="mt-3">
                                            <a href="{{ route('member.enrollments.show', $activeEnrollment) }}" class="text-yellow-800 dark:text-yellow-200 underline">
                                                View My Active Enrollment →
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                @if($isEnrolled)
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">You are enrolled in this class</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Your enrollment has been approved. Check your enrollments for more details.</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('member.enrollments.show', $enrollment) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                View Enrollment
                                            </a>
                                            <a href="{{ route('member.enrollments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                                My Enrollments
                                            </a>
                                        </div>
                                    </div>
                                @elseif(!$hasActiveEnrollment && !$class->isFull() && $class->is_active)
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Join This Class</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Enroll in this discipleship class to participate in sessions. You can only enroll in one class at a time.</p>
                                        </div>
                                        <a href="{{ route('member.enrollments.create', $class) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Enroll Now
                                        </a>
                                    </div>
                                @elseif(!$hasActiveEnrollment && $class->isFull())
                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded p-4">
                                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                            <strong>Class Full:</strong> This class has reached its capacity. Please check back later or browse other classes.
                                        </p>
                                    </div>
                                @elseif(!$hasActiveEnrollment && !$class->is_active)
                                    <div class="bg-gray-50 dark:bg-gray-700/20 border border-gray-200 dark:border-gray-800 rounded p-4">
                                        <p class="text-sm text-gray-800 dark:text-gray-200">
                                            <strong>Class Inactive:</strong> This class is not currently active. Please browse other available classes.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
            @endauth

            <!-- Class Content (for enrolled members) -->
            @if($canAccessContent && isset($groupedContents) && $groupedContents->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Class Content</h3>
                            @can('manageSessions', $class)
                                <a href="{{ route('classes.content.index', $class) }}" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Manage All Content →
                                </a>
                            @endcan
                        </div>
                        
                        <div class="space-y-6">
                            @foreach($groupedContents as $weekNumber => $weekItems)
                                <div>
                                    <h4 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                        @if($weekNumber)
                                            Week {{ $weekNumber }}
                                        @else
                                            General Content
                                        @endif
                                    </h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($weekItems as $contentItem)
                                            @php
                                                $state = $contentProgressStates[$contentItem->id] ?? ['completed' => false, 'locked' => false];
                                                $isCompleted = $state['completed'] ?? false;
                                                $isLocked = $state['locked'] ?? false;
                                            @endphp

                                            @if($isLocked)
                                                <div class="block border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/40 opacity-60 cursor-not-allowed">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                            @if($contentItem->content_type === 'outline') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                                            @elseif($contentItem->content_type === 'lesson') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                            @elseif($contentItem->content_type === 'assignment') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                                            @elseif($contentItem->content_type === 'homework') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                            @elseif($contentItem->content_type === 'resource') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                                            @endif">
                                                            {{ \App\Models\ClassContent::getContentTypes()[$contentItem->content_type] ?? ucfirst($contentItem->content_type) }}
                                                        </span>
                                                        <span class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 .552-.224 1.052-.586 1.414A1.99 1.99 0 0110 13c-.552 0-1.052-.224-1.414-.586A1.99 1.99 0 018 11c0-.552.224-1.052.586-1.414A1.99 1.99 0 0110 9c.552 0 1.052.224 1.414.586.362.362.586.862.586 1.414z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21c4.418 0 8-4.03 8-9s-3.582-9-8-9-8 4.03-8 9 3.582 9 8 9z" />
                                                            </svg>
                                                            Locked
                                                        </span>
                                                    </div>
                                                    <h5 class="font-semibold text-gray-700 dark:text-gray-200 mb-1 line-clamp-2">
                                                        {{ $contentItem->title }}
                                                    </h5>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                        Complete the previous lesson to unlock this content.
                                                    </p>
                                                </div>
                                            @else
                                                <a href="{{ route('classes.content.show', [$class, $contentItem]) }}" class="block border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                            @if($contentItem->content_type === 'outline') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                                            @elseif($contentItem->content_type === 'lesson') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                            @elseif($contentItem->content_type === 'assignment') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                                            @elseif($contentItem->content_type === 'homework') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                            @elseif($contentItem->content_type === 'resource') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                                            @endif">
                                                            {{ \App\Models\ClassContent::getContentTypes()[$contentItem->content_type] ?? ucfirst($contentItem->content_type) }}
                                                        </span>
                                                        @if($isCompleted)
                                                            <span class="inline-flex items-center text-xs text-green-600 dark:text-green-300">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                                Completed
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    <h5 class="font-semibold text-gray-900 dark:text-gray-100 mb-1 line-clamp-2">
                                                        {{ $contentItem->title }}
                                                    </h5>
                                                    
                                                    @if($contentItem->content)
                                                        <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2">
                                                            {{ Str::limit(strip_tags($contentItem->content), 80) }}
                                                        </p>
                                                    @endif
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @auth
                            @if(auth()->user()->isMember())
                                @php
                                    $member = \App\Models\Member::where('user_id', auth()->id())->first();
                                    $isEnrolled = $member && $member->isEnrolledInClass($class);
                                    $hasActiveEnrollment = $member && $member->hasActiveEnrollment();
                                @endphp
                                @if(!$isEnrolled && !$hasActiveEnrollment && !$class->isFull() && $class->is_active)
                                    <a href="{{ route('member.enrollments.create', $class) }}" class="flex flex-col items-center p-6 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors border-2 border-green-500 shadow-md">
                                        <svg class="w-12 h-12 text-green-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                        </svg>
                                        <span class="text-base font-bold text-green-900 dark:text-green-100">Enroll in Class</span>
                                        <span class="text-xs text-green-700 dark:text-green-300 mt-1 text-center">Join this discipleship class now</span>
                                    </a>
                                @elseif($isEnrolled)
                                    <a href="{{ route('classes.sessions.index', $class) }}" class="flex flex-col items-center p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors border-2 border-blue-500">
                                        <svg class="w-12 h-12 text-blue-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <span class="text-base font-bold text-blue-900 dark:text-blue-100">View Sessions</span>
                                        <span class="text-xs text-blue-700 dark:text-blue-300 mt-1 text-center">Access your class content</span>
                                    </a>
                                @endif
                            @endif
                        @endauth
                        
                        @can('manageSessions', $class)
                            <a href="{{ route('classes.sessions.create', $class) }}" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span class="text-sm font-medium text-blue-600 dark:text-blue-400">Add Session</span>
                            </a>
                        @endcan

                        @can('viewAttendance', $class)
                            <a href="{{ route('classes.schedule', $class) }}" class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                                <svg class="w-8 h-8 text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 9l6-6m0 0v6m0-6h-6"></path>
                                </svg>
                                <span class="text-sm font-medium text-green-600 dark:text-green-400">View Schedule</span>
                            </a>
                        @endcan

                        @if(auth()->user()->isAdmin() || auth()->user()->isPastor())
                            @can('update', $class)
                                <a href="{{ route('classes.edit', $class) }}" class="flex flex-col items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
                                    <svg class="w-8 h-8 text-yellow-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Edit Class</span>
                                </a>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
