<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\DiscipleshipClass;
use App\Models\Member;
use App\Models\Mentorship;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get attendance trends over time
     */
    public function getAttendanceTrends(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->subMonths(6);
        $endDate = $endDate ?? Carbon::now();

        $trends = Attendance::whereHas('classSession', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('session_date', [$startDate, $endDate]);
        })
            ->selectRaw('DATE(class_sessions.session_date) as date, COUNT(*) as count')
            ->join('class_sessions', 'attendance.class_session_id', '=', 'class_sessions.id')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => (int) $item->count,
                ];
            });

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'trends' => $trends,
            'total_attendance' => $trends->sum('count'),
            'average_per_day' => $trends->count() > 0 ? round($trends->sum('count') / $trends->count(), 2) : 0,
        ];
    }

    /**
     * Get member engagement analytics
     */
    public function getMemberEngagement(): array
    {
        $totalMembers = Member::count();
        $membersWithAttendance = Member::whereHas('attendance')->distinct()->count();
        $membersInClasses = Member::whereHas('enrollments')->distinct()->count();
        $membersInMentorships = Member::whereHas('mentorships')->distinct()->count();

        $engagementRate = $totalMembers > 0
            ? round((($membersWithAttendance + $membersInClasses + $membersInMentorships) / ($totalMembers * 3)) * 100, 2)
            : 0;

        $topEngagedMembers = Member::withCount(['attendance', 'enrollments', 'mentorships'])
            ->orderByDesc('attendance_count')
            ->orderByDesc('enrollments_count')
            ->orderByDesc('mentorships_count')
            ->take(10)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'attendance_count' => $member->attendance_count,
                    'enrollments_count' => $member->enrollments_count,
                    'mentorships_count' => $member->mentorships_count,
                    'total_engagement' => $member->attendance_count + $member->enrollments_count + $member->mentorships_count,
                ];
            });

        return [
            'total_members' => $totalMembers,
            'members_with_attendance' => $membersWithAttendance,
            'members_in_classes' => $membersInClasses,
            'members_in_mentorships' => $membersInMentorships,
            'engagement_rate' => $engagementRate,
            'top_engaged_members' => $topEngagedMembers,
        ];
    }

    /**
     * Get class performance metrics
     */
    public function getClassPerformance(): array
    {
        $totalClasses = DiscipleshipClass::count();
        $activeClasses = DiscipleshipClass::where('is_active', true)->count();
        $completedClasses = DiscipleshipClass::where('is_active', false)->count();

        $classesWithSessions = DiscipleshipClass::whereHas('sessions')->count();
        $totalSessions = ClassSession::count();
        $sessionsWithAttendance = ClassSession::whereHas('attendance')->count();

        $averageAttendanceRate = $this->calculateAverageAttendanceRate();

        $topPerformingClasses = DiscipleshipClass::withCount(['sessions', 'enrollments'])
            ->with(['sessions' => function ($query) {
                $query->withCount('attendance');
            }])
            ->get()
            ->map(function ($class) {
                $totalPossibleAttendance = $class->sessions_count * $class->enrollments_count;
                $actualAttendance = $class->sessions->sum('attendance_count');
                $attendanceRate = $totalPossibleAttendance > 0
                    ? round(($actualAttendance / $totalPossibleAttendance) * 100, 2)
                    : 0;

                return [
                    'id' => $class->id,
                    'title' => $class->title,
                    'sessions_count' => $class->sessions_count,
                    'enrollments_count' => $class->enrollments_count,
                    'attendance_rate' => $attendanceRate,
                ];
            })
            ->sortByDesc('attendance_rate')
            ->take(10)
            ->values();

        return [
            'total_classes' => $totalClasses,
            'active_classes' => $activeClasses,
            'completed_classes' => $completedClasses,
            'classes_with_sessions' => $classesWithSessions,
            'total_sessions' => $totalSessions,
            'sessions_with_attendance' => $sessionsWithAttendance,
            'average_attendance_rate' => $averageAttendanceRate,
            'top_performing_classes' => $topPerformingClasses,
        ];
    }

    /**
     * Get mentorship success tracking
     */
    public function getMentorshipSuccess(): array
    {
        $totalMentorships = Mentorship::count();
        $activeMentorships = Mentorship::where('status', 'active')->count();
        $completedMentorships = Mentorship::where('status', 'completed')->count();
        $pausedMentorships = Mentorship::where('status', 'paused')->count();

        $successRate = $totalMentorships > 0
            ? round(($completedMentorships / $totalMentorships) * 100, 2)
            : 0;

        $averageDurationResult = Mentorship::whereNotNull('end_date')
            ->selectRaw('AVG(DATEDIFF(end_date, start_date)) as avg_days')
            ->first();
        
        $averageDuration = $averageDurationResult && $averageDurationResult->avg_days
            ? (float) $averageDurationResult->avg_days
            : 0;

        return [
            'total_mentorships' => $totalMentorships,
            'active_mentorships' => $activeMentorships,
            'completed_mentorships' => $completedMentorships,
            'paused_mentorships' => $pausedMentorships,
            'success_rate' => $successRate,
            'average_duration_days' => round($averageDuration, 2),
        ];
    }

    /**
     * Calculate average attendance rate across all classes
     */
    protected function calculateAverageAttendanceRate(): float
    {
        $classes = DiscipleshipClass::withCount(['sessions', 'enrollments'])
            ->with(['sessions' => function ($query) {
                $query->withCount('attendance');
            }])
            ->get();

        if ($classes->isEmpty()) {
            return 0.0;
        }

        $totalRate = 0;
        $classCount = 0;

        foreach ($classes as $class) {
            $totalPossibleAttendance = $class->sessions_count * $class->enrollments_count;
            if ($totalPossibleAttendance > 0) {
                $actualAttendance = $class->sessions->sum('attendance_count');
                $rate = ($actualAttendance / $totalPossibleAttendance) * 100;
                $totalRate += $rate;
                $classCount++;
            }
        }

        return $classCount > 0 ? round($totalRate / $classCount, 2) : 0.0;
    }
}

