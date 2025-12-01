<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Welcome back, {{ auth()->user()->name }}!
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Daily Bible Verse -->
            @if(isset($dailyVerse))
                <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-indigo-900/30 dark:via-purple-900/30 dark:to-pink-900/30 border-l-4 border-indigo-500 rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="bg-indigo-100 dark:bg-indigo-900/50 rounded-lg p-3">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-semibold text-indigo-900 dark:text-indigo-100 uppercase tracking-wide">
                                        Daily Bible Verse
                                    </h3>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                        {{ $dailyVerse['date'] ?? date('F d, Y') }}
                                    </span>
                                </div>
                                
                                <blockquote class="text-lg text-gray-800 dark:text-gray-200 leading-relaxed italic mb-3">
                                    "{{ $dailyVerse['verse'] ?? 'For I know the plans I have for you, declares the LORD, plans to prosper you and not to harm you, plans to give you hope and a future.' }}"
                                </blockquote>
                                
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
                                        — {{ $dailyVerse['reference'] ?? 'Jeremiah 29:11' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Overview Cards - Only for Admin and Pastor, not Mentors -->
            @if($user->isAdmin() || $user->isPastor())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Members -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Total Members
                                    </dt>
                                    <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ number_format($totalMembers) }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Classes -->
                    @can('viewAny', App\Models\DiscipleshipClass::class)
                    <a href="{{ route('classes.index') }}" class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow cursor-pointer">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Active Classes
                                    </div>
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ number_format($totalClasses) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    @else
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Active Classes
                                    </dt>
                                    <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ number_format($totalClasses) }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endcan

                    <!-- Total Sessions -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 9l6-6m0 0v6m0-6h-6"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Total Sessions
                                    </dt>
                                    <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ number_format($totalSessions) }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Rate -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    @if($user->isMember())
                                        My Attendance Rate
                                    @else
                                        Attendance Rate
                                    @endif
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $attendanceRate }}%
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Role-specific Information -->
            @if($user->isAdmin())
                <!-- Admin System Analytics -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                System Analytics
                            </h3>
                            <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                View Full Admin Dashboard →
                            </a>
                        </div>
                        
                        <!-- User Statistics -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $userStats['admin'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Admins</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $userStats['pastor'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Pastors</div>
                            </div>
                            <div class="text-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-amber-500">{{ $userStats['mentor'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Mentors</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ $userStats['member'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Members</div>
                            </div>
                        </div>

                        <!-- Charts Grid -->
                        @if(isset($analytics['charts']))
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Member Growth Chart -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Member Growth (Last 12 Months)</h4>
                                <div style="height: 250px;">
                                    <canvas id="memberGrowthChart"></canvas>
                                </div>
                            </div>

                            <!-- Attendance by Month Chart -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Monthly Attendance (Last 12 Months)</h4>
                                <div style="height: 250px;">
                                    <canvas id="attendanceChart"></canvas>
                                </div>
                            </div>

                            <!-- Daily Attendance Chart -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Daily Attendance Trends</h4>
                                <div style="height: 250px;">
                                    <canvas id="dailyAttendanceChart"></canvas>
                                </div>
                            </div>

                            <!-- Messages Over Time Chart -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Messages Sent (Last 6 Months)</h4>
                                <div style="height: 250px;">
                                    <canvas id="messagesChart"></canvas>
                                </div>
                            </div>

                            <!-- Mentorship Status Chart -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Mentorship Status Distribution</h4>
                                <div style="height: 250px;">
                                    <canvas id="mentorshipStatusChart"></canvas>
                                </div>
                            </div>

                            <!-- Class Performance Chart -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Top Performing Classes</h4>
                                <div style="height: 250px;">
                                    <canvas id="classPerformanceChart"></canvas>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @elseif($user->isPastor())
                <!-- Pastor Dashboard Layout - Reorganized Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Recent Activity & Today's Sessions -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Recent Activity -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                    Recent Activity (30 days)
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">New Members</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $recentMembers }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Sessions Held</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $recentSessions }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Active Mentorships</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $activeMentorships }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Today's Sessions -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                    Today's Sessions
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Sessions Today</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $todaySessions }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Attendance Today</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $todayAttendance }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: My Ministry Overview -->
                    <div class="lg:col-span-2">
                        <div class="bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50 dark:from-indigo-900/30 dark:via-blue-900/30 dark:to-purple-900/30 border border-indigo-200 dark:border-indigo-700 rounded-xl shadow-lg overflow-hidden h-full">
                            <div class="p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        My Ministry Overview
                                    </h3>
                                    <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    @can('viewAny', App\Models\DiscipleshipClass::class)
                                    <a href="{{ route('classes.index') }}" class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer border-2 border-transparent hover:border-indigo-400">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-2">{{ $myClasses ?? 0 }}</div>
                                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">My Classes</div>
                                    </a>
                                    @else
                                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border-2 border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-2">{{ $myClasses ?? 0 }}</div>
                                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">My Classes</div>
                                    </div>
                                    @endcan
                                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border-2 border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 9l6-6m0 0v6m0-6h-6"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="text-4xl font-bold text-green-600 dark:text-green-400 mb-2">{{ $mySessions ?? 0 }}</div>
                                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">My Sessions</div>
                                    </div>
                                    <a href="{{ route('members.index') }}" class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer border-2 border-transparent hover:border-indigo-400">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center group-hover:bg-purple-600 transition-colors">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="text-4xl font-bold text-purple-600 dark:text-purple-400 mb-2">{{ $myMembers ?? 0 }}</div>
                                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">My Members</div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($user->isMentor())
                <!-- My Classes/Members - For Mentors -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            My Ministry
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @can('viewAny', App\Models\DiscipleshipClass::class)
                            <a href="{{ route('classes.index') }}" class="text-center hover:opacity-75 transition-opacity cursor-pointer p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $myClasses ?? 0 }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">My Classes</div>
                            </a>
                            @else
                            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $myClasses ?? 0 }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">My Classes</div>
                            </div>
                            @endcan
                            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $mySessions ?? 0 }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">My Sessions</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ $myMembers ?? 0 }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">My Members</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Member-specific Quick Actions -->
            @if($user->isMember())
                @php
                    $member = $member ?? \App\Models\Member::where('user_id', $user->id)->first();
                    $myEnrollments = $member ? ($member->enrollments()->where('status', 'approved')->count()) : 0;
                    $pendingEnrollments = $member ? $member->enrollments()->where('status', 'pending')->count() : 0;
                @endphp
                
                @if($member)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                My Enrollments
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ $myEnrollments }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Active Enrollments</div>
                                </div>
                                <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                    <div class="text-2xl font-bold text-yellow-600">{{ $pendingEnrollments }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Pending Requests</div>
                                </div>
                                <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <a href="{{ route('member.enrollments.index') }}" class="block hover:opacity-75 transition-opacity">
                                        <div class="text-sm font-medium text-green-600 mb-1">View All</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">My Enrollments</div>
                                    </a>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('classes.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Browse Available Classes
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Recent Activity & Today's Sessions - Only for Admin, not Pastors (Pastors have it in their grid) -->
            @if($user->isAdmin())
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Activity -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Recent Activity (30 days)
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">New Members</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $recentMembers }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Sessions Held</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $recentSessions }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Active Mentorships</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $activeMentorships }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Sessions -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Today's Sessions
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Sessions Today</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $todaySessions }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Attendance Today</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $todayAttendance }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions (Only for Admin and Pastor, not Mentors) -->
            @if($user->isAdmin() || $user->isPastor())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Quick Actions
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            @if($user->canManageMembers())
                                <a href="{{ route('members.create') }}" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                    <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-blue-600">Add Member</span>
                                </a>
                            @endif

                            @if($user->canManageClasses())
                                <a href="{{ route('classes.create') }}" class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                                    <svg class="w-8 h-8 text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-green-600">Create Class</span>
                                </a>
                            @endif

                        @if($user->canManageClasses())
                            <a href="{{ route('classes.index') }}" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                                <svg class="w-8 h-8 text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 9l6-6m0 0v6m0-6h-6"></path>
                                </svg>
                                <span class="text-sm font-medium text-purple-600">Mark Attendance</span>
                            </a>
                        @else
                            <a href="{{ route('classes.index') }}" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                                <svg class="w-8 h-8 text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 9l6-6m0 0v6m0-6h-6"></path>
                                </svg>
                                <span class="text-sm font-medium text-purple-600">View Sessions</span>
                            </a>
                        @endif

                        @if($user->canManageMembers())
                            <a href="{{ route('messages.create') }}" class="flex flex-col items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
                                <svg class="w-8 h-8 text-yellow-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-yellow-600">Send Message</span>
                            </a>
                        @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Analytics Dashboard - Only for Admin and Pastor, not Mentors -->
            @if(isset($analytics) && !empty($analytics) && ($user->isAdmin() || $user->isPastor()))
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Analytics Dashboard
                        </h3>
                        <a href="{{ route('reports.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                            View Full Reports →
                        </a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Member Engagement -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Member Engagement</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Members</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $analytics['member_engagement']['total_members'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">With Attendance</span>
                                    <span class="text-sm font-semibold text-green-600">{{ $analytics['member_engagement']['members_with_attendance'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">In Classes</span>
                                    <span class="text-sm font-semibold text-blue-600">{{ $analytics['member_engagement']['members_in_classes'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">In Mentorships</span>
                                    <span class="text-sm font-semibold text-purple-600">{{ $analytics['member_engagement']['members_in_mentorships'] ?? 0 }}</span>
                                </div>
                                <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Engagement Rate</span>
                                        <span class="text-sm font-bold text-indigo-600">{{ number_format($analytics['member_engagement']['engagement_rate'] ?? 0, 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Class Performance -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Class Performance</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Classes</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $analytics['class_performance']['total_classes'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Active Classes</span>
                                    <span class="text-sm font-semibold text-green-600">{{ $analytics['class_performance']['active_classes'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Sessions</span>
                                    <span class="text-sm font-semibold text-blue-600">{{ $analytics['class_performance']['total_sessions'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Sessions with Attendance</span>
                                    <span class="text-sm font-semibold text-purple-600">{{ $analytics['class_performance']['sessions_with_attendance'] ?? 0 }}</span>
                                </div>
                                <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Avg Attendance Rate</span>
                                        <span class="text-sm font-bold text-indigo-600">{{ number_format($analytics['class_performance']['average_attendance_rate'] ?? 0, 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mentorship Success -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Mentorship Success</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Mentorships</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $analytics['mentorship_success']['total_mentorships'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Active</span>
                                    <span class="text-sm font-semibold text-green-600">{{ $analytics['mentorship_success']['active_mentorships'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Completed</span>
                                    <span class="text-sm font-semibold text-blue-600">{{ $analytics['mentorship_success']['completed_mentorships'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Paused</span>
                                    <span class="text-sm font-semibold text-yellow-600">{{ $analytics['mentorship_success']['paused_mentorships'] ?? 0 }}</span>
                                </div>
                                <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Success Rate</span>
                                        <span class="text-sm font-bold text-indigo-600">{{ number_format($analytics['mentorship_success']['success_rate'] ?? 0, 1) }}%</span>
                                    </div>
                                </div>
                                @if(isset($analytics['mentorship_success']['average_duration_days']) && $analytics['mentorship_success']['average_duration_days'] > 0)
                                <div class="pt-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Avg Duration</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($analytics['mentorship_success']['average_duration_days'], 0) }} days</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Attendance Trends Summary -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Attendance Trends (Last 3 Months)</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Attendance</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $analytics['attendance_trends']['total_attendance'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Average per Day</span>
                                    <span class="text-sm font-semibold text-blue-600">{{ number_format($analytics['attendance_trends']['average_per_day'] ?? 0, 1) }}</span>
                                </div>
                                <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Period: {{ \Carbon\Carbon::parse($analytics['attendance_trends']['period']['start'] ?? now())->format('M d, Y') }} - 
                                        {{ \Carbon\Carbon::parse($analytics['attendance_trends']['period']['end'] ?? now())->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Performing Classes -->
                    @if(isset($analytics['class_performance']['top_performing_classes']) && count($analytics['class_performance']['top_performing_classes']) > 0)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Top Performing Classes</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Class</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sessions</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Enrollments</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Attendance Rate</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach(array_slice($analytics['class_performance']['top_performing_classes']->toArray(), 0, 5) as $class)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $class['title'] }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $class['sessions_count'] }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $class['enrollments_count'] }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold text-indigo-600">{{ number_format($class['attendance_rate'], 1) }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

    @if(isset($analytics['charts']))
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($analytics['charts']);
            if (typeof initDashboardCharts === 'function') {
                initDashboardCharts(chartData);
            }
        });
    </script>
    @endpush
    @endif
</x-app-layout>
